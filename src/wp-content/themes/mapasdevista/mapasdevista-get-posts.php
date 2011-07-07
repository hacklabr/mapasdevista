<?php


add_action('wp_ajax_nopriv_mapasdevista_get_posts', 'mapasdevista_get_posts');
add_action('wp_ajax_mapasdevista_get_posts', 'mapasdevista_get_posts');


function mapasdevista_get_posts() {

    $mapinfo = get_post_meta($_POST['page_id'], '_mapasdevista', true);
    
    if (!is_array($mapinfo['post_types']))
        return; // nothing to show 
    
    if ($_POST['get'] == 'totalPosts') {
    
    
        echo 4;
    
    
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
        
        foreach ($posts as $post) {
            $meta = get_post_meta($post->ID, '_mpv_location', true);
            $terms = wp_get_object_terms( $post->ID, $mapinfo['taxonomies'] );
            $postsResponse[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'date' => $post->post_date,
                'location' => $meta,
                'terms' => $terms
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
