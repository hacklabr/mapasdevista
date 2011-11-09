<?php

add_action('wp_ajax_nopriv_mapasdevista_get_posts', 'mapasdevista_get_posts_ajax');
add_action('wp_ajax_mapasdevista_get_posts', 'mapasdevista_get_posts_ajax');

add_action('wp_ajax_nopriv_mapasdevista_get_post', 'mapasdevista_get_post_ajax');
add_action('wp_ajax_mapasdevista_get_post', 'mapasdevista_get_post_ajax');

function mapasdevista_get_post_ajax($p = null) {

    if (is_null($p) || !$p || strlen($p) == 0)
        $p = $_POST['post_id'];
        
    if (!is_numeric($p))
        die('error');
        
    query_posts('post_type=any&p='.$p);
    
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            mapasdevista_get_template('mapasdevista-loop-opened');
        }
    } else {
        die('error');
    }
    
    die();

}

function mapasdevista_get_posts_ajax() {
    
    $mapinfo = get_post_meta($_POST['page_id'], '_mapasdevista', true);

    if (!is_array($mapinfo['post_types']))
        return; // nothing to show

    if ($_POST['get'] == 'totalPosts') {


        global $wpdb;


        foreach ($mapinfo['post_types'] as $i => $p) {
            $mapinfo['post_types'][$i] = "'$p'";
        }

        $pt = implode(',', $mapinfo['post_types']);
        
        $search_query = '';
        
        if (isset($_POST['search']) && $_POST['search'] != '') {
            $serach_for = '%' . $_POST['search'] . '%';
            $search_query = $wpdb->prepare( "AND (post_title LIKE %s OR post_content LIKE %s )", $serach_for, $serach_for  );
        }
        
        if ($_POST['api'] == 'image') {
            $q = "SELECT COUNT(DISTINCT(post_id)) FROM $wpdb->postmeta JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_type IN ($pt) AND post_status = 'publish' AND meta_key = '_mpv_in_img_map' AND meta_value = '{$_POST['page_id']}' $search_query";
        } else {
            $q = "SELECT COUNT(post_id) FROM $wpdb->postmeta JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_type IN ($pt) AND post_status = 'publish' AND meta_key = '_mpv_inmap' AND meta_value = '{$_POST['page_id']}' $search_query";
        }

        $total = $wpdb->get_var($q);

        echo $total;


    } elseif ($_POST['get'] == 'posts') {

        if ($_POST['api'] == 'image') {

            $args = array(
                'numberposts'     => $_POST['posts_per_page'],
                'offset'          => $_POST['offset'],
                'orderby'         => 'post_date',
                'order'           => 'DESC',
                'meta_key'        => '_mpv_in_img_map',
                'meta_value'      => $_POST['page_id'],
                'post_type'       => $mapinfo['post_types'],
            );

        } else {
            $args = array(
                'numberposts'     => $_POST['posts_per_page'],
                'offset'          => $_POST['offset'],
                'orderby'         => 'post_date',
                'order'           => 'DESC',
                'meta_key'        => '_mpv_inmap',
                'meta_value'      => $_POST['page_id'],
                'post_type'       => $mapinfo['post_types'],
            );
        }
        
        if (isset($_POST['search']) && $_POST['search'] != '')
            $args['s'] = $_POST['search'];
        
        $posts = get_posts($args);

        $postsResponse = array();

        $number = $_POST['offset'];

        foreach ($posts as $post) {


            if ($_POST['api'] == 'image') {

                $meta = get_post_meta($post->ID, '_mpv_img_coord_' . $_POST['page_id'], true);
                $meta = explode(',', $meta);

                $location = array();
                $location['lon'] = floatval($meta[0]);
                $location['lat'] = floatval($meta[1]);

                $pin_id = get_post_meta($post->ID, '_mpv_img_pin_' . $_POST['page_id'], true);
                $pin = wp_get_attachment_image_src($pin_id, 'full');
                $pin['clickable'] = get_post_meta($pin_id, '_pin_clickable', true) !== 'no';

            } else {

                $location = get_post_meta($post->ID, '_mpv_location', true);

                // wordpress doesn't serialize data correctly and openlayers
                // only accept float values for latitude and longitude
                if (isset($location['lat']) && isset($location['lon'])) {
                    $location['lat'] = floatval($location['lat']);
                    $location['lon'] = floatval($location['lon']);
                }

                $pin_id = get_post_meta($post->ID, '_mpv_pin', true);
                $pin = wp_get_attachment_image_src($pin_id, 'full');
                $pin['anchor'] = get_post_meta($pin_id, '_pin_anchor', true);
                $pin['clickable'] = get_post_meta($pin_id, '_pin_clickable', true) !== 'no';
                
            }

            
                    
            $number ++;
            $terms = wp_get_object_terms( $post->ID, $mapinfo['taxonomies'] );

            
            
            $pResponse = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'date' => $post->post_date,
                'location' => $location,
                'terms' => $terms,
                'post_type' => $post->post_type,
                'number' => $number,
                'author' => $post->post_author,
                'pin' => $pin
                
            );
            
            if($post->post_type == 'page' && get_post_meta($post->ID, '_mapasdevista')){
                $pResponse['link'] = get_post_permalink($post->ID);
            }
            
            
            $postsResponse[] = $pResponse;


        }

        $newoffset = (int) $_POST['offset'] + sizeof($posts) < (int) $_POST['total'] ? (int) $_POST['offset'] + (int) $_POST['posts_per_page'] : 'end';

        $response = array(

            'newoffset' => $newoffset,
            'posts' => $postsResponse

        );

        echo json_encode($response);


    } 

    die();

}

function has_clickable_pin($post_id=null) {
    global $post;

    if (is_null($post_id) || !is_numeric($post_id)) {
        if (isset($post->ID) && is_numeric($post->ID))
            $post_id = $post->ID;
        else
            return false;
    }
    $pin_id = get_post_meta($post_id, '_mpv_pin', true);
    return get_post_meta($pin_id, '_pin_clickable', true) !== 'no';
}

function the_pin($post_id = null, $page_id = null) {

    global $current_map_page_id, $post;
    
    if (!is_numeric($current_map_page_id))
        return false;
    
    if (is_null($post_id) || !is_numeric($post_id)) {
        if (isset($post->ID) && is_numeric($post->ID))
            $post_id = $post->ID;
        else
            return false;
    }
    
    $mapinfo = get_post_meta($current_map_page_id, '_mapasdevista', true);
    
    if ($mapinfo['api'] == 'image') {
        $pin_id = get_post_meta($post_id, '_mpv_img_pin_' . $current_map_page_id, true);
                
    } else {
        $pin_id = get_post_meta($post_id, '_mpv_pin', true);
        
    }
    
    echo wp_get_attachment_image($pin_id);
    
}
