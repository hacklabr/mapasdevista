<?php


add_action( 'add_meta_boxes', 'mapasdevista_add_custom_box' );


add_action( 'save_post', 'mapasdevista_save_postdata' );


function mapasdevista_add_custom_box() {

        // Lets check which post types have to have a map

        // If it is displayed in at least one map, there will be a metabox to place the post on the map

        $maps = mapasdevista_get_maps();

        $post_types = array();

        foreach ($maps as $map) {
            if (is_array($map['post_types']) && $map['api'] != 'image')
                foreach ($map['post_types'] as $p_type)
                    array_push($post_types, $p_type);
        }

        $post_types = array_unique($post_types);

        foreach ($post_types as $post_type)
            add_meta_box( 'mapasdevista_metabox', __( 'Place it on the map', 'mapasdevista' ), 'mapasdevista_metabox_map', $post_type );


        // And there will also be one meta box for each map that uses an image as a map.
        $post_types = array();
        foreach ($maps as $map) {

            if ($map['api'] == 'image') {
                if (is_array($map['post_types']))
                    foreach ($map['post_types'] as $p_type)
                        array_push($post_types, $p_type);

            }

        }

        $post_types = array_unique($post_types);
            foreach ($post_types as $post_type)
                add_meta_box( 'mapasdevista_metabox_image', __( 'Place it on the map', 'mapasdevista' ), 'mapasdevista_metabox_image', $post_type );

}

/**
 * Renderiza o Google Maps na pagina de posts
 */
function mapasdevista_metabox_map() {
    global $post, $post_type;
    if( !$location=get_post_meta($post->ID, '_mpv_location', true) ) {
        $location = array('lat'=>'', 'lon'=>'');
    }
    $post_pin = get_post_meta($post->ID, '_mpv_pin', true);

    $args = array(
        'post_type' => 'attachment',
        'meta_key' => '_pin_anchor',
        'posts_per_page' => '-1'
    );
    $pins = get_posts($args);

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


    <h4><?php _e("Available pins", "mapasdevista");?></h4>
    <div class="iconlist">
        <script type="text/javascript">var pinsanchor = { };</script>
        <?php foreach($pins as $pin): $pinanchor = json_encode(get_post_meta($pin->ID, '_pin_anchor', true)); ?>
            <div class="icon">
                <script type="text/javascript">pinsanchor.pin_<?php echo $pin->ID;?>=<?php echo $pinanchor;?>;</script>
                <div class="icon-image"><label for="pin_<?php echo $pin->ID;?>">
                    <?php echo wp_get_attachment_image($pin->ID, 'full', false, array('style'=>'max-width:64px;max-height:64px;'));?>
                </label></div>
                <div class="icon-info">
                <input type="radio" name="mpv_pin" id="pin_<?php echo $pin->ID;?>" value="<?php echo $pin->ID;?>"<?php if($post_pin==$pin->ID) echo ' checked';?>/>
                    <span class="icon-name"><?php echo $pin->post_name;?></span>
                </div>
            </div>
        <?php endforeach;?>
    </div>
    
    <h4><?php _e("Maps", "mapasdevista");?></h4>
    <p><?php _e('Mark the maps in which this post is going to appear', 'mapasdevista'); ?></p>
    <ul>
        <?php $maps = mapasdevista_get_maps(); ?>
        <?php $inmaps = get_post_meta($post->ID, '_mpv_inmap'); if (!is_array($inmaps)) $inmaps = array(); ?>
        <?php foreach ($maps as $map): ?>
            <?php if (is_array($map['post_types']) && $map['api'] != 'image' && in_array($post_type, $map['post_types'])): ?>
            
                <li>
                    <input type="checkbox" name="mpv_inmap[]" value="<?php echo $map['page_id']; ?>" id="inmap_<?php echo $map['page_id']; ?>" <?php if (in_array($map['page_id'], $inmaps)) echo 'checked'; ?> />
                    <label for="inmap_<?php echo $map['page_id']; ?>">
                        <?php echo $map['name']; ?>
                    </label>
                </li>
                
            <?php endif; ?>
            
        <?php endforeach; ?>
        
        
            
    
    </ul>
    
    <div class="clear"></div>

    <?php
}

/**
 * Save from metabox
 */
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
    
    delete_post_meta($post_id, '_mpv_inmap');
    delete_post_meta($post_id, '_mpv_in_img_map');
    if(isset($_POST['mpv_inmap']) && is_array($_POST['mpv_inmap'])) {
        foreach($_POST['mpv_inmap'] as $page_id ) {
            if(is_numeric($page_id)) {
                $page_id = intval($page_id);
                add_post_meta($post_id, "_mpv_inmap", $page_id); 
            }
        }
    }
    
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

    if(isset($_POST['mpv_pin']) && is_numeric($_POST['mpv_pin'])) {
        $pin_id = intval(sprintf("%d", $_POST['mpv_pin']));
        if($pin_id > 0) {
            update_post_meta($post_id, '_mpv_pin', $pin_id);
        }
    }

    if(isset($_POST['mpv_img_pin']) && is_array($_POST['mpv_img_pin'])) {
        foreach($_POST['mpv_img_pin'] as $page_id => $pin_id) {
            if(is_numeric($page_id) && is_numeric($pin_id)) {
                $page_id = intval($page_id);
                $pin_id = intval($pin_id);
                update_post_meta($post_id, "_mpv_img_pin_{$page_id}", $pin_id);
            }
        }
    }

    if(isset($_POST['mpv_img_coord']) && is_array($_POST['mpv_img_coord'])) {
        foreach($_POST['mpv_img_coord'] as $page_id => $coord) {
            if(is_numeric($page_id) && preg_match('/^(-?[0-9]+),(-?[0-9]+)$/', $coord, $coord)) {
                $page_id = intval($page_id);
                $coord = "{$coord[1]},{$coord[2]}";
                update_post_meta($post_id, "_mpv_img_coord_{$page_id}", $coord);
                add_post_meta($post_id, "_mpv_in_img_map", $page_id);
            }
        }
    } 
}


function mapasdevista_metabox_image() {
    global $post_type, $post;
    $mapsToDisplay = array();

    $args = array(
        'post_type' => 'attachment',
        'meta_key' => '_pin_anchor',
    );
    $pins = get_posts($args);
?>
    <div class="iconlist" id="image-maps">
        <?php
        $maps = mapasdevista_get_maps();
        $inmaps = get_post_meta($post->ID, '_mpv_in_img_map'); 
        
        if (!is_array($inmaps)) $inmaps = array();
        
        foreach ($maps as $map):
            if (is_array($map['post_types']) && $map['api'] == 'image' && in_array($post_type, $map['post_types'])): ?>
            
            <?php
            $image_id = get_post_meta($map['page_id'], '_thumbnail_id', true);
            $image_full_src = wp_get_attachment_image_src($image_id, 'full');
            $image_full_src = $image_full_src[0];
            ?>

            <div class="icon">
                <div class="icon-image">
                    <?php echo get_the_post_thumbnail($map['page_id'], array(64,64), array('id'=>"im-{$map['page_id']}"));?>
                    <input type="hidden" class="full_image_src" value="<?php echo $image_full_src;?>" />
                </div>
                <div class="icon-info">
                    <input type="checkbox" name="mpv_img_coord[<?php echo $map['page_id']?>]"
                                           id="mpv_img_coord_<?php echo $map['page_id']?>"
                                           value="<?php echo get_post_meta($post->ID, "_mpv_img_coord_{$map['page_id']}", true); ?>" 
                                           <?php if (in_array($map['page_id'], $inmaps)) echo 'checked';  ?>
                                           />

                    <label for="mpv_img_coord_<?php echo $map['page_id']?>"><?php echo $map['name']?></label>

                    <input type="hidden" name="mpv_img_pin[<?php echo $map['page_id']?>]"
                                         id="mpv_img_pin_<?php echo $map['page_id']?>"
                                         value="<?php echo get_post_meta($post->ID, "_mpv_img_pin_{$map['page_id']}", true); ?>" />
                </div>
            </div>

        <?php endif; endforeach;?>
    </div>
    <div class="clear"></div>

    <div id="dialog">
        <div class="iconlist">
            <?php foreach($pins as $pin): ?>
                <div class="icon" id="icon-<?php echo $pin->ID;?>"><?php echo wp_get_attachment_image($pin->ID, array(64,64));?></div>
            <?php endforeach;?>
        </div>
        <div class="panel"><img class="pin"/></div>
    </div>

<?php
}
