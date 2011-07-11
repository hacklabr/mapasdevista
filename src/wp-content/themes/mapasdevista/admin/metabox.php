<?php


add_action( 'add_meta_boxes', 'mapasdevista_add_custom_box' );


add_action( 'save_post', 'mapasdevista_save_postdata' );


function mapasdevista_add_custom_box() {

        // Lets check which post types have to have a map

        // If it is displayed in at least one map, there will be a metabox to place the post on the map

        $maps = mapasdevista_get_maps();

        $post_types = array();

        foreach ($maps as $map) {
            if (is_array($map['post_types']))
                foreach ($map['post_types'] as $p_type)
                    array_push($post_types, $p_type);
        }

        $post_types = array_unique($post_types);

        foreach ($post_types as $post_type)
            add_meta_box( 'mapasdevista_metabox', __( 'Place it on the map', 'mapasdevista' ), 'mapasdevista_metabox_map', $post_type );


        // And there will also be one meta box for each map that uses an image as a map.
        foreach ($maps as $map) {

            if ($map['type'] == 'image') {
                if (is_array($map['post_types']))
                    foreach ($map['post_types'] as $p_type)
                        add_meta_box( 'mapasdevista_metabox', sprintf( __( 'Place it on the map %s%', 'mapasdevista' ), $map['name'] ), 'mapasdevista_metabox_image', $p_type );

            }

        }

}

/**
 * Renderiza o Google Maps na pagina de posts
 */
function mapasdevista_metabox_map() {
    global $post;
    if( !$location=get_post_meta($post->ID, '_mpv_location', true) ) {
        $location = array('lat'=>'', 'lon'=>'');
    }

    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'mapasdevista_noncename' );
    ?>
    <fieldset>
        <label for="mpv_lat"><?php _e('Latitude', 'mpv');?>:</label>
        <input type="text" class="medium-field" name="mpv_lat" id="mpv_lat" value="<?php echo $location['lat'];?>"/>

        <label for="mpv_lon"><?php _e('Longitude', 'mpv');?>:</label>
        <input type="text" class="medium-field" name="mpv_lon" id="mpv_lon" value="<?php echo $location['lon'];?>"/>

        <input type="button" id="mpv_load_coords" value="Exibir"/>
    </fieldset>
    <div id="mpv_canvas" class="mpv_canvas"></div>
    <fieldset>
        <label for="mpv_search_address"><?php _e('Search address', 'mpv');?>:</label>
        <input type="text" id="mpv_search_address" class="large-field"/>
    </fieldset>

    <?php
}


function mapasdevista_save_postdata($post_id) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

    if ( !wp_verify_nonce( $_POST['mapasdevista_noncename'], plugin_basename( __FILE__ ) ) )
            return;

    global $wp_post_types;
    $cap = $wp_post_types[$_POST['post_type']]->cap->edit_post;

    if ( !current_user_can( $cap, $post_id ) )
        return;

    // save
    global $post;
    if(isset($_POST['mpv_lat']) && isset($_POST['mpv_lon'])) {
        $location = array();
        $location['lat'] = floatval(sprintf("%f", $_POST['mpv_lat']));
        $location['lon'] = floatval(sprintf("%f", $_POST['mpv_lon']));

        if($location['lat'] !== floatval(0) && $location['lon'] !== floatval(0)) {
            update_post_meta($post_id, '_mpv_location', $location);
        } else {
            delete_post_meta($post_id, '_mpv_location');
        }
    }
}
