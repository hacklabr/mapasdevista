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


function mapasdevista_metabox_map() {

    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'mapasdevista_noncename' );

    echo 'caixa';
}

/* When the post is saved, saves our custom data */
function mapasdevista_save_postdata( $post_id ) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if ( !wp_verify_nonce( $_POST['mapasdevista_noncename'], plugin_basename( __FILE__ ) ) )
            return;

    
    // Check permissions
    global $wp_post_types;
    
    $cap = $wp_post_types[$_POST['post_type']]->cap->edit_post;
    
    if ( !current_user_can( $cap, $post_id ) )
        return;
    
    // save

    
}
