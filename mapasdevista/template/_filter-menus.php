<?php

add_filter('wp_nav_menu_objects', 'mapasdevista_change_menu_links', 10, 2);

function mapasdevista_change_menu_links($objects, $args) {
    foreach ($objects as $o) {
        // we are going to change the behavior of the link only if the link does not point to a page that is another map or an outside link
        $change = true;
        if ($o->object == 'page') {
            if (get_post_meta($o->object_id, '_mapasdevista', true))
                $change = false;
        } elseif ($o->object == 'custom') {
            $change = false;
        }
        
        if ($change) {
            $o->classes = empty( $o->classes ) ? array() : (array) $o->classes;
		    $o->classes[] = 'js-menu-link-to-post';
        }
        
    }
    return $objects;
    

}

add_filter('nav_menu_item_id', 'mapasdevista_change_menu_item_ids', 10, 3);

function mapasdevista_change_menu_item_ids($id, $item, $args) {
    return 'menu-item-' . $item->object_id;
}
