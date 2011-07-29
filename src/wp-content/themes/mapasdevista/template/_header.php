<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <title><?php
            global $page, $paged;
            wp_title( '|', true, 'right' );
            bloginfo( 'name' );
            $site_description = get_bloginfo( 'description', 'display' );
            ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        
        <style>
            <?php include('style.css.php'); ?>
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
        
        <!-- TODO: só aparecer se tiver alguma coisa no menu -->
        <div id="toggle-side-menu">
            <?php mapasdevista_image("side-menu.png", array("id" => "toggle-side-menu-icon")); ?>
        </div>
        
        <?php wp_nav_menu( array( 'container_class' => 'map-menu-side', 'theme_location' => 'mapasdevista_side', 'fallback_cb' => false ) ); ?>
        <div id="toggle-results">
            <?php mapasdevista_image("show-results.png", array("id" => "hide-results", "alt" => "Esconder Resultados")); ?>
        </div>
