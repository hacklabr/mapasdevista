<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php
    wp_title( '|', true, 'right' );
    bloginfo( 'name' );

    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        echo " | $site_description";
    ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
</head>

<body <?php body_class(); ?>>

<div class="wrapper">
    <div class="info">

        <?php 
        if ($_POST['install'] == 1) {

            if (current_user_can('manage_options')) {

                $args = $_POST; // in the future we can choose some options to the map
                if ($feedback = mapasdevista_create_homepage_map($args) === true) {
                    wp_redirect(site_url());
                    exit;
                } else { ?>
                    <p class="error"><?php _e('Error creating the map: ', 'mapasdevista'); ?></p>
                <?php
                    echo $feedback;
                }

            } else { ?>
                <p class="error"><?php _e('You don\'t have permission to do that', 'mapasdevista'); ?></p>
            <?php
            }

        }
        ?>

        <p><?php _e('Hi there! In order to start using your map:', 'mapasdevista'); ?></p>
        <p><?php _e('1. set up a page as your home page', 'mapasdevista'); ?><br/><?php _e('2. create a map in this page', 'mapasdevista'); ?></p>

        <form method="post">
            <p class="bottom"><?php _e('Or click here and I\'ll do it for you:', 'mapasdevista'); ?>
                <input type="hidden" name="install" value="1">
                <input type="submit" value="<?php _e('Create my first map for me', 'mapasdevista'); ?>" />
            </p>
        </form>
    </div>
</div>



</body>
</html>