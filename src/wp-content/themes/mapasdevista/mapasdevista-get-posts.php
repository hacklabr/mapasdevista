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
        
        
        $total = $wpdb->get_var("SELECT COUNT(post_id) FROM $wpdb->postmeta JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_type IN ($pt) AND post_status = 'publish' AND meta_key = '_mpv_location'");
        
        
        echo $total;
    
    
    } elseif ($_POST['get'] == 'posts') {
    
        
        $args = array(
            'numberposts'     => $_POST['posts_per_page'],
            'offset'          => $_POST['offset'],
            'orderby'         => 'post_date',
            'order'           => 'DESC',
            'meta_key'        => '_mpv_location',
            'post_type'       => $mapinfo['post_types'],
        ); 
        
        $posts = get_posts($args);
        
        $postsResponse = array();
        
        $number = $_POST['offset'];
        
        foreach ($posts as $post) {
            $number ++;
            $meta = get_post_meta($post->ID, '_mpv_location', true);
            $terms = wp_get_object_terms( $post->ID, $mapinfo['taxonomies'] );
            $postsResponse[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'date' => $post->post_date,
                'location' => $meta,
                'terms' => $terms,
                'post_type' => $post->post_type,
                'number' => $number
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
