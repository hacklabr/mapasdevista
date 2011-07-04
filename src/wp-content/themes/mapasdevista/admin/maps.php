<?php

add_action('init', 'mapasdevista_save_map');

function mapasdevista_save_map() {

    if ($_POST['submit_map']) {

        $error = false;

        // check if there is already another map associated with this page
        if ($_POST['original_page_id'] != $_POST['page_id'] && get_post_meta($_POST['page_id'], '_mapasdevista', true)) {
            $error = __('Ther is another map already in this page. Please choose another page.', 'mapasdevista');
        }

        if (!$error) {

            //remove association with other page
            if (is_numeric($_POST['original_page_id'])) {
                delete_post_meta('_mapasdevista', $_POST['original_page_id']);
            }

            update_post_meta($_POST['page_id'], '_mapasdevista', $_POST['map']);

            wp_redirect(add_query_arg(array('action' => '', 'message' => 'save_success')));

        } else {

            add_action('all_admin_notices', 'mapasdevista_save_map_error_notice');

            function mapasdevista_save_map_error_notice() {
                echo '<div class="error"><p>';
                _e('There is another map already in this page. Please choose another page.', 'mapasdevista');
                echo '</p></div>';

            }

        }

    }

}



function mapasdevista_maps_page() {
    global $wp_post_types, $wp_taxonomies;
    ?>


    <div class="wrap">
        <h2><?php _e('Maps', 'mapasdevista'); ?></h2>
        <?php if ($_GET['action'] == 'edit' || $_GET['action'] == 'new') : ?>

            <?php

            if ($_GET['action'] == 'edit' && $_GET['page_id']) {

                $map = get_post_meta($_GET['page_id'], '_mapasdevista', true);

            } else {

                if ($error)
                    $map = $_POST['map'];

            }

            if (!is_array($map))
                $map = array();
            if (!is_array($map['post_types']))
                $map['post_types'] = array();
            if (!is_array($map['taxonomies']))
                $map['taxonomies'] = array();
            if (!is_array($map['filters']))
                $map['filters'] = array();

            ?>
            <pre>
            <?php //print_r($map); ?>
            </pre>
            <form method="POST">

            <h3><?php _e('Select the page that will be the placeholder for this map', 'mapasdevista'); ?></h3>
            <?php wp_dropdown_pages( 'selected=' . $_GET['page_id'] ); ?>

            <input type="hidden" name="original_page_id" value="<?php echo $_GET['page_id']; ?>" />

            <h3><?php _e('Map Name', 'mapasdevista'); ?></h3>
            <input type="text" name="map[name]" value="<?php echo $map['name']; ?>">

            <h3><?php _e('Map API', 'mapasdevista'); ?></h3>
            <input type="radio" name="map[api]" value="google"<?php if ($map['type'] == 'google') echo ' checked'; ?> > Google Maps <br />
            <input type="radio" name="map[api]" value="openlayers"<?php if ($map['type'] == 'openlayers') echo ' checked'; ?> > OpenLayers <br />
            <input type="radio" name="map[api]" value="image"<?php if ($map['type'] == 'image') echo ' checked'; ?> > <?php _e('Image as map', 'mapasdevista'); ?>

            <fieldset id="mpv_map_fields">
                <h3><?php _e('Map initial state', 'mapasdevista'); ?></h3>

                <label><?php _e('Initial posistion', 'mapasdevista'); ?>:</label>
                <ul>
                    <li>
                        <label for="mpv_lat" class="small"><?php _e('Latitude', 'mapasdevista');?>:</label>
                        <input type="text" class="small-field" name="map[coord][lat]" id="mpv_lat" value="<?php echo $map['coord']['lat'];?>"/>
                    </li>
                    <li>
                        <label for="mpv_lon" class="small"><?php _e('Longitude', 'mapasdevista');?>:</label>
                        <input type="text" class="small-field" name="map[coord][lng]" id="mpv_lng" value="<?php echo $map['coord']['lng'];?>"/>
                    </li>
                    <li>
                        <label for="mpv_zoom" class="small">Zoom level:</label>
                        <input type="text" class="small-field" name="map[zoom]" id="mpv_zoom"/>
                    </li>
                </ul>

                <label><?php _e('Map type', 'mapasdevista'); ?></label>
                <ul>
                    <li>
                        <input type="radio" name="mpv_map_type" id="mpv_map_type_road" value="road"<?php echo $map['type']=='road'?' checked="checked"':'';?>/>
                        <label for="mpv_map_type_road" class="small"><?php _e('Road', 'mapasdevista');?>:</label>
                    </li>
                    <li>
                        <input type="radio" name="mpv_map_type" id="mpv_map_type_satellite" value="satellite"<?php echo $map['type']=='satellite'?' checked="checked"':'';?>/>
                        <label for="mpv_map_type_satellite" class="small"><?php _e('Satellite', 'mapasdevista');?>:</label>
                    </li>
                    <li>
                        <input type="radio" name="mpv_map_type" id="mpv_map_type_hybrid" value="hybrid"<?php echo $map['type']=='hybrid'?' checked="checked"':'';?>/>
                        <label for="mpv_map_type_hybrid" class="small"><?php _e('Hybrid', 'mapasdevista');?>:</label>
                    </li>
                </ul>
            </fieldset>
            <div id="mpv_canvas"></div>

            <script type="text/javascript">
            (function($) {
                mapstraction = new mxn.Mapstraction('mpv_canvas','openlayers');
                mapstraction.setCenterAndZoom(new mxn.LatLonPoint(-23.531095, -46.673999), 16);

                mapstraction.addControls({
                    'pan': true,
                    'map_type': true
                });

                var map_type = 'road';
                try{
                    if($('input[name=mpv_map_type]:checked')) {
                        map_type = $('input[name=mpv_map_type]:checked').val().toUpperCase();
                        if(mxn.Mapstraction[map_type])
                            mapstraction.setMapType(mxn.Mapstraction[map_type]);
                    }
                } catch(e) {
                    $('input#mpv_map_type_'+map_type).attr('checked','checked');
                    $('input[name=mpv_map_type][value!='+map_type+']').attr('disabled','disabled');
                }
            })(jQuery);
            </script>

            <h3><?php _e('What kind of posts may appear on the map?', 'mapasdevista'); ?></h3>

            <?php foreach ($wp_post_types as $type_name => $type) : ?>

                <?php if ($type_name == 'attachment' || $type_name == 'revision' || $type_name == 'nav_menu_item' ) continue; ?>
                <input type="checkbox" name="map[post_types][]" value="<?php echo $type_name; ?>" <?php if (in_array($type_name, $map['post_types'])) echo 'checked'; ?> > <?php echo $type->label; ?> <br />

            <?php endforeach; ?>

            <h3><?php _e('What filters should be visible to the visitors?', 'mapasdevista'); ?></h3>

            <input type="checkbox" name="map[filters][]" value="new" <?php if (in_array('new', $map['filters'])) echo 'checked'; ?> > <?php _e('Filter by new posts', 'mapasdevista'); ?> <br />
            <input type="checkbox" name="map[filters][]" value="post_types" <?php if (in_array('post_types', $map['filters'])) echo 'checked'; ?> > <?php _e('Filter by post types', 'mapasdevista'); ?> <br />
            <br/>
            <?php _e('Filter by the following taxonomies', 'mapasdevista'); ?>
            <br/>
            <?php foreach ($wp_taxonomies as $type_name => $type) : ?>

                <?php if ($type_name == 'link_category' || $type_name == 'nav_menu') continue; ?>
                <input type="checkbox" name="map[taxonomies][]" value="<?php echo $type_name; ?>" <?php if (in_array($type_name, $map['taxonomies'])) echo 'checked'; ?> > <?php echo $type->label; ?> <br />

            <?php endforeach; ?>

            <input type="submit" name="submit_map" value="<?php _e('Save Changes', 'mapasdevista'); ?>" />

            </form>

        <?php else: ?>

            <?php if ($_GET['message'] == 'save_success'): ?>
                <div class="updated">
                <p>
                <?php _e('Map Saved', 'mapasdevista'); ?>
                </p>
                </div>
            <?php endif; ?>

            <?php
            $maps = mapasdevista_get_maps();
            ?>

            <table class="widefat fixed ">
                <thead>
                <tr class="column-title">
                    <th> <?php _e('Map name', 'mapasdevista'); ?></th>
                    <th> <?php _e('Page', 'mapasdevista'); ?></th>
                    <th> </th>
                </tr>
                </thead>

                <?php foreach ($maps as $m): ?>

                    <?php

                    ?>

                    <tr>
                        <td> <a href="<?php echo add_query_arg( array( 'action' => 'edit', 'page_id' => $m['page_id'] ) ) ; ?>"> <?php echo $m['name']; ?> </a> </td>
                        <td> <a href="<?php echo get_permalink( $m['page_id'] ); ?>"> <?php echo get_the_title( $m['page_id'] ); ?> </a> </td>
                        <td> </td>
                    </tr>


                <?php endforeach; ?>


            </table>

            <br /><br />

            <a href="<?php echo add_query_arg('action', 'new'); ?>"> <?php _e('Add New Map', 'mapasdevista'); ?> </a>

        <?php endif; ?>

    </div>


    <?php

}
