<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        
        <meta name="google" value="notranslate"> <!--  this avoids problems with hash change and the google chrome translate bar -->
        
        <title><?php
            global $page, $paged;
            wp_title( '|', true, 'right' );
            bloginfo( 'name' );
            $site_description = get_bloginfo( 'description', 'display' );
            ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        
        <style type="text/css">
            <?php include( mapasdevista_get_template('template/style.css', null, false) ); ?>
        </style>
        
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        
        <div id="post_overlay">
            <a id="close_post_overlay" title="Fechar"><?php mapasdevista_image("close.png", array("alt" => "Fechar")); ?></a>
            <div id="post_overlay_content">
            </div>
        </div>
        
        <div id="map">
        
        </div>

        <div id="blog-title">
            <a href="<?php echo get_bloginfo('siteurl'); ?>">
                <img src="<?php echo get_theme_option('header_image'); ?>" />
            </a>
        </div>
        <?php wp_nav_menu( array( 'container_class' => 'map-menu-top', 'theme_location' => 'mapasdevista_top', 'fallback_cb' => false ) ); ?>
        
        
        <?php $menu_positions = get_theme_mod('nav_menu_locations'); ?>
        <?php if (isset($menu_positions['mapasdevista_side']) && $menu_positions['mapasdevista_side'] != '0'): ?>
            <div id="toggle-side-menu">
                <?php mapasdevista_image("side-menu.png", array("id" => "toggle-side-menu-icon")); ?>
            </div>
        <?php endif; ?>

        <div id="posts-loader">
            <span id="posts-loader-loaded">0</span>/<span id="posts-loader-total">0</span> <span><?php _e('items loaded', 'mapasdevista'); ?></span>
        </div>
        
        <?php wp_nav_menu( array( 'container_class' => 'map-menu-side', 'theme_location' => 'mapasdevista_side', 'fallback_cb' => false ) ); ?>
        
        
        
        <div id="toggle-results">
            <?php mapasdevista_image("show-results.png", array("id" => "hide-results", "alt" => "Esconder Resultados")); ?>
        </div>
