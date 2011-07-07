<?php


add_action('wp_ajax_nopriv_mapasdevista_get_posts', 'mapasdevista_get_posts');
add_action('wp_ajax_mapasdevista_get_posts', 'mapasdevista_get_posts');


function mapasdevista_get_posts() {

    echo 'aa';
    
    die();

}
