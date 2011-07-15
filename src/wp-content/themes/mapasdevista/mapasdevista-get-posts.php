<?php


add_action('wp_ajax_nopriv_mapasdevista_get_posts', 'mapasdevista_get_posts');
add_action('wp_ajax_mapasdevista_get_posts', 'mapasdevista_get_posts');


function mapasdevista_get_posts() {

    $mapinfo = get_post_meta($_POST['page_id'], '_mapasdevista', true);
    
    if (!is_array($mapinfo['post_types']))
        return; // nothing to show 
    
    if ($_POST['get'] == 'totalPosts') {
    
    
        global $wpdb;
        
        
        foreach ($mapinfo['post_types'] as $i => $p) {
            $mapinfo['post_types'][$i] = "'$p'";
        }
        
        $pt = implode(',', $mapinfo['post_types']);
        
        if ($_POST['api'] == 'image') {
            $q = "SELECT COUNT(DISTINCT(post_id)) FROM $wpdb->postmeta JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_type IN ($pt) AND post_status = 'publish' AND meta_key = '_mpv_in_img_map' AND meta_value = '{$_POST['page_id']}'";
        } else {
            $q = "SELECT COUNT(post_id) FROM $wpdb->postmeta JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_type IN ($pt) AND post_status = 'publish' AND meta_key = '_mpv_location'";
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
                'meta_key'        => '_mpv_location',
                'post_type'       => $mapinfo['post_types'],
            );
        }
        
        $posts = get_posts($args);
        
        $postsResponse = array();
        
        $number = $_POST['offset'];
        
        foreach ($posts as $post) {
            
            
            if ($_POST['api'] == 'image') {
            
                $meta = get_post_meta($post->ID, '_mpv_img_coord_' . $_POST['page_id'], true);
                $meta = explode(',', $meta);
                
                $location = array();
                $location['lon'] = $meta[0];
                $location['lat'] = $meta[1];
                
                $pin_id = get_post_meta($post->ID, '_mpv_img_pin_' . $_POST['page_id'], true);
                $pin = wp_get_attachment_image_src($pin_id);
                

            
            } else {
            
                $location = get_post_meta($post->ID, '_mpv_location', true);
                $pin_id = get_post_meta($post->ID, '_mpv_pin', true);
                $pin = wp_get_attachment_image_src($pin_id);
                $pin['anchor'] = get_post_meta($pin_id, '_pin_anchor', true);
                
                
            
            }
            
            $number ++;
            $terms = wp_get_object_terms( $post->ID, $mapinfo['taxonomies'] );
            
            $postsResponse[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'date' => $post->post_date,
                'location' => $location,
                'terms' => $terms,
                'post_type' => $post->post_type,
                'number' => $number,
                'pin' => $pin
            );
            
            
            
            
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
