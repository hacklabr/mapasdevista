<?php

add_action('init', 'mapasdevista_save_map');

function mapasdevista_save_map() {

    if (isset($_POST['submit_map'])) {

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
        <?php if (isset($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'new')) : ?>

            <?php

            if ($_GET['action'] == 'edit' && $_GET['page_id']) {

                $map = get_post_meta($_GET['page_id'], '_mapasdevista', true);

            } else {

                if ($error)
                    $map = $_POST['map'];

            }

            if (!is_array($map))
                $map = array();
            if (!(isset($map['post_types']) && is_array($map['post_types'])))
                $map['post_types'] = array();
            if (!(isset($map['taxonomies']) && is_array($map['taxonomies'])))
                $map['taxonomies'] = array();
            if (!(isset($map['filters']) && is_array($map['filters'])))
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
            <ul id="mpv_map_api">
                <li>
                    <input type="radio" name="map[api]" id="mpv_api_googlev3" value="googlev3"<?php if ($map['api'] == 'googlev3') echo ' checked'; ?>>
                    <label for="mpv_api_googlev3">Google Maps</label>
                </li>
                <li>
                    <input type="radio" name="map[api]" id="mpv_api_openlayers" value="openlayers"<?php if ($map['api'] == 'openlayers') echo ' checked'; ?>>
                    <label for="mpv_api_openlayers">OpenLayers</label>
                </li>
                <li>
                    <input type="radio" name="map[api]" id="mpv_api_image" value="image"<?php if ($map['api'] == 'image') echo ' checked'; ?>>
                    <label for="mpv_api_image"><?php _e('Image as map', 'mapasdevista'); ?></label>
                </li>
            </ul>

            <fieldset id="mpv_map_fields">
                <h3><?php _e('Map initial state', 'mapasdevista'); ?></h3>

                <label><?php _e('Initial posistion', 'mapasdevista'); ?>:</label>
                <ul id="mpv_map_status">
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
                        <input type="text" class="small-field" name="map[zoom]" id="mpv_zoom" value="<?php echo $map['zoom'];?>"/>
                    </li>
                    <li><input type="button" id="mapbutton" value="Center map"/></li>
                </ul>

                <label><?php _e('Map type', 'mapasdevista'); ?></label>
                <ul id="mpv_map_type">
                    <li>
                        <input type="radio" name="map[type]" id="mpv_map_type_road" value="road"<?php echo $map['type']=='road'?' checked="checked"':'';?>/>
                        <label for="mpv_map_type_road" class="small"><?php _e('Road', 'mapasdevista');?>:</label>
                    </li>
                    <li>
                        <input type="radio" name="map[type]" id="mpv_map_type_satellite" value="satellite"<?php echo $map['type']=='satellite'?' checked="checked"':'';?>/>
                        <label for="mpv_map_type_satellite" class="small"><?php _e('Satellite', 'mapasdevista');?>:</label>
                    </li>
                    <li>
                        <input type="radio" name="map[type]" id="mpv_map_type_hybrid" value="hybrid"<?php echo $map['type']=='hybrid'?' checked="checked"':'';?>/>
                        <label for="mpv_map_type_hybrid" class="small"><?php _e('Hybrid', 'mapasdevista');?>:</label>
                    </li>
                </ul>
            </fieldset>
            <div id="mpv_canvas_googlev3" class="mpv_canvas" style="display:none"></div>
            <div id="mpv_canvas_openlayers" class="mpv_canvas" style="display:none"></div>

            <script type="text/javascript">
            (function($) {
                function fill_fields(lat, lng, zoom) {
                    $("#mpv_lat").val(lat);
                    $("#mpv_lng").val(lng);
                    $("#mpv_zoom").val(zoom);
                }

                function centerMapAndZoom() {
                    try {
                        var point = new mxn.LatLonPoint(
                                        parseFloat($("#mpv_lat").val()),
                                        parseFloat($("#mpv_lng").val())
                                    );
                        mapstraction.setCenterAndZoom(point, parseInt($("#mpv_zoom").val()));
                    } catch(e) {
                        if(console && console.log) console.log(e);
                        mapstraction.setCenterAndZoom(new mxn.LatLonPoint(-23.531095, -46.673999), 16);
                        fill_fields(mapstraction.getCenter().lat, mapstraction.getCenter().lon, mapstraction.getZoom());
                    }
                }

                // carrega o mapa
                var api = 'openlayers';
                if($('#mpv_map_api input:checked').val()) {
                    api = $('#mpv_map_api input:checked').val();
                } else {
                    $('#mpv_map_api input[value='+api+']').attr('checked','checked');
                }
                $('#mpv_canvas_'+api).show();
                mapstraction = new mxn.Mapstraction('mpv_canvas_'+api, api);
                centerMapAndZoom();

                mapstraction.addControls({
                    'pan': true,
                    'map_type': true
                });

                // eventos do mapa
                mapstraction.changeZoom.addHandler(function(n,s,a) {
                    $("#mpv_zoom").val(s.getZoom());
                });
                mapstraction.endPan.addHandler(function(n,s,a) {
                        fill_fields(s.getCenter().lat, s.getCenter().lon, s.getZoom());
                });
                $("#mpv_map_status input[type=button]").click(function(e){centerMapAndZoom();});

                // este trecho determina o tipo do mapa (road|satellite|hybrid)
                var map_type = 'road';
                try{
                    if($('#mpv_map_type input:checked').length == 1) {
                        map_type = $('#mpv_map_type input:checked').val();
                    }
                    var mxn_map_type = map_type.toUpperCase();
                    if(mxn.Mapstraction[mxn_map_type])
                        mapstraction.setMapType(mxn.Mapstraction[mxn_map_type]);
                } catch(e) {
                    $('input#mpv_map_type_'+map_type).attr('checked','checked');
                    $('#mpv_map_type input[value!='+map_type+']').attr('disabled','disabled');
                }

                // este evento muda o tipo do mapa conforme o selecionado
                $('#mpv_map_type input').change(function(e) {
                    mxn_map_type = $(this).val().toUpperCase();
                    mapstraction.setMapType(mxn.Mapstraction[mxn_map_type]);
                });

                // ação para troca de api
                $('#mpv_map_api input').click(function(e) {
                    if($(this).val()) {
                        api = $(this).val();
                    }
                    mapstraction.swap('mpv_canvas_'+api, api);
                    if(api == 'openlayers'){
                        $('input#mpv_map_type_road').attr('checked','checked');
                        $('#mpv_map_type input[value!=road]').attr('disabled','disabled');
                    } else if(api == 'googlev3') {
                        $('#mpv_map_type input[value!=road]').attr('disabled',false);
                    }
                    map_type = ['road','satellite','hybrid'][mapstraction.getMapType()-1];
                    $('input#mpv_map_type_'+map_type).attr('checked','checked');
                });
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

            <?php if (isset($_GET['message']) && $_GET['message'] == 'save_success'): ?>
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
