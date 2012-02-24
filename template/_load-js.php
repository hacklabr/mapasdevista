<?php

wp_enqueue_script( 'mapasdevista', mapasdevista_get_baseurl() . '/js/front-end.js', array('jquery') );
wp_enqueue_script( 'ajax-comments', mapasdevista_get_baseurl() . '/js/ajax-comments.js', array('jquery', 'jquery-form') );
wp_localize_script( 'ajax-comments', 'messages', array(
    'loading' => __('Loading...', 'mapasdevista'),
    'empty_name' => __('Please enter your name.', 'mapasdevista'),
    'empty_email' => __('Please enter your email address.', 'mapasdevista'),
    'invalid_email' => __('Please enter a valid email address.', 'mapasdevista'),
    'empty_comment' => __('Please enter your comment', 'mapasdevista'),
    'comment_success' => __('Your comment has been added.', 'mapasdevista'),
    'error' => __('Error!', 'mapasdevista'),
    'show_filters' => __('Show Filters', 'mapasdevista'),
    'hide_filters' => __('Hide Filters', 'mapasdevista')
));

wp_enqueue_script( 'comment-reply' );

if ($mapinfo['api'] == 'image') {

    $image_src = get_post_meta(get_the_ID(), '_thumbnail_id', true);
    
    $image_src = wp_get_attachment_image_src($image_src, 'full');
    $image_src = $image_src[0];

    wp_localize_script( 'mapasdevista', 'mapinfo', array(
        'image_src' => $image_src,
        'api' => $mapinfo['api'],
        'ajaxurl' => admin_url('admin-ajax.php'),
        'page_id' => get_the_ID(),
        'baseurl' => mapasdevista_get_baseurl(),
        'search' => $_GET['mapasdevista_search']

    ) );



} else {
    $min_zoom = isset($mapinfo['min_zoom']) && is_numeric($mapinfo['min_zoom']) ? $mapinfo['min_zoom'] : 0;
    $max_zoom = isset($mapinfo['max_zoom']) && is_numeric($mapinfo['max_zoom']) ? $mapinfo['max_zoom'] : 0;
    
    $sw_lng = isset($mapinfo['south_west']['lng']) && is_numeric($mapinfo['south_west']['lng']) ? $mapinfo['south_west']['lng'] : 0;
    $sw_lat = isset($mapinfo['south_west']['lat']) && is_numeric($mapinfo['south_west']['lat']) ? $mapinfo['south_west']['lat'] : 0;
    $ne_lng = isset($mapinfo['north_east']['lng']) && is_numeric($mapinfo['north_east']['lng']) ? $mapinfo['north_east']['lng'] : 0;
    $ne_lat = isset($mapinfo['north_east']['lat']) && is_numeric($mapinfo['north_east']['lat']) ? $mapinfo['north_east']['lat'] : 0;
    
    $mapinfovars = array(
        
        'api' => $mapinfo['api'],
        'lat' => $mapinfo['coord']['lat'],
        'lng' => $mapinfo['coord']['lng'],
        'zoom' => $mapinfo['zoom'],
        'type' => $mapinfo['type'],
        'ajaxurl' => admin_url('admin-ajax.php'),
        'page_id' => get_the_ID(),
        'baseurl' => mapasdevista_get_baseurl(),
        'min_zoom' => $min_zoom,
        'max_zoom' => $max_zoom,
        'sw_lng' => $sw_lng,
        'sw_lat' => $sw_lat,
        'ne_lng' => $ne_lng,
        'ne_lat' => $ne_lat,
        'control_zoom' => $mapinfo['control'] && $mapinfo['control']['zoom'] != 'none' ? $mapinfo['control']['zoom'] : 'false',
        'control_pan' =>  $mapinfo['control'] && $mapinfo['control']['pan'] ? 'true' : 'false',
        'control_map_type' =>  $mapinfo['control'] && $mapinfo['control']['map_type'] ? 'true' : 'false',
    );
    
    if ( isset($_GET['mapasdevista_search']) && $_GET['mapasdevista_search'] != '')
        $mapinfovars['search'] = $_GET['mapasdevista_search'];
    
    wp_localize_script( 'mapasdevista', 'mapinfo',  $mapinfovars);

}


wp_enqueue_script('mapstraction', mapasdevista_get_baseurl('template_directory') . '/js/mxn/mxn-min.js' );
wp_enqueue_script('mapstraction-core', mapasdevista_get_baseurl('template_directory') . '/js/mxn/mxn.core-min.js');

if ($mapinfo['api'] == 'openlayers') {
    wp_enqueue_script('openlayers', 'http://openlayers.org/api/OpenLayers.js');
    wp_enqueue_script('mapstraction-openlayers', mapasdevista_get_baseurl('template_directory') . '/js/mxn/mxn.openlayers.core-min.js');
} elseif ($mapinfo['api'] == 'googlev3') {
    
    $googleapikey = get_theme_option('google_key');
    $googleapikey = $googleapikey ? "&key=$googleapikey" : '';
    wp_enqueue_script('google-maps-v3', 'http://maps.google.com/maps/api/js?sensor=false' . $googleapikey);
    wp_enqueue_script('mapstraction-googlev3', mapasdevista_get_baseurl('template_directory') . '/js/mxn/mxn.googlev3.core-min.js');
    wp_enqueue_script('google-infobox', mapasdevista_get_baseurl('template_directory') . '/js/mxn/infobox_packed.js', array('mapstraction-googlev3'));
    
} elseif ($mapinfo['api'] == 'image') {
    wp_enqueue_script('mapstraction-image', mapasdevista_get_baseurl('template_directory') . '/js/mxn/mxn.image.core.js');
}
