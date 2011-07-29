<?php

global $wp_post_types;

$obj = get_queried_object();

$mapinfo = get_post_meta($obj->ID, '_mapasdevista', true);

global $current_map_page_id;
$current_map_page_id = get_the_ID();
