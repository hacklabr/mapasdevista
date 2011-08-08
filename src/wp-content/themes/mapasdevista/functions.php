<?php

// includes
include('admin/maps.php');
include('admin/pins.php');
include('admin/theme.php');
include('admin/metabox.php');
include('template/ajax.php');

add_action( 'after_setup_theme', 'mapasdevista_setup' );
if ( ! function_exists( 'mapasdevista_setup' ) ):

    function mapasdevista_setup() {

        // Post Format support. You can also use the legacy "gallery" or "asides" (note the plural) categories.
        add_theme_support( 'post-formats', array( 'gallery', 'image', 'video' /*, 'audio' */ ) );

        // This theme uses post thumbnails
        add_theme_support( 'post-thumbnails' );

        // Make theme available for translation
        // Translations can be filed in the /languages/ directory
        load_theme_textdomain( 'mapasdevista', TEMPLATEPATH . '/languages' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( array(
            'mapasdevista_top' => __( 'Map Menu (top)', 'mapasdevista' ),
            'mapasdevista_side' => __( 'Map Menu (side)', 'mapasdevista' )
        ) );

    }

endif;

add_action( 'admin_menu', 'mapasdevista_admin_menu' );

function mapasdevista_admin_menu() {

    add_submenu_page('mapasdevista_maps', __('Maps', 'mapasdevista'), __('Maps', 'mapasdevista'), 'manage_maps', 'mapasdevista_maps', 'mapasdevista_maps_page');
    add_menu_page(__('Maps of view', 'mapasdevista'), __('Maps of view', 'mapasdevista'), 'manage_maps', 'mapasdevista_maps', 'mapasdevista_maps_page',null,30);
    add_submenu_page('mapasdevista_maps', __('Pins', 'mapasdevista'), __('Pins', 'mapasdevista'), 'manage_maps', 'mapasdevista_pins_page', 'mapasdevista_pins_page');
    add_submenu_page('mapasdevista_maps', __('Settings', 'mapasdevista'), __('Settings', 'mapasdevista'), 'manage_maps', 'mapasdevista_theme_page', 'mapasdevista_theme_page');

}

add_action( 'init', 'mapasdevista_init' );

function mapasdevista_init() {
    global $pagenow;

    if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
        $adm = get_role('administrator');
        $adm->add_cap( 'manage_maps' );
        $adm->add_cap( 'post_item_on_map' );
    }
}


add_action( 'admin_init', 'mapasdevista_admin_init' );

function mapasdevista_admin_init() {
    global $pagenow;
    
    
    
    if($pagenow === "post.php" || $pagenow === "post-new.php" || (isset($_GET['page']) && $_GET['page'] === "mapasdevista_maps")) {
        // api do google maps versao 3 direto 
        $googleapikey = get_theme_option('google_key');
        $googleapikey = $googleapikey ? "&key=$googleapikey" : '';
        wp_enqueue_script('google-maps-v3', 'http://maps.google.com/maps/api/js?sensor=false' . $googleapikey);

        wp_enqueue_script('openlayers', 'http://openlayers.org/api/OpenLayers.js');

        wp_enqueue_script('mapstraction', mapasdevista_get_baseurl() . '/js/mxn/mxn-min.js' );
        wp_enqueue_script('mapstraction-core', mapasdevista_get_baseurl() . '/js/mxn/mxn.core-min.js');
        wp_enqueue_script('mapstraction-googlev3', mapasdevista_get_baseurl() . '/js/mxn/mxn.googlev3.core-min.js');
        wp_enqueue_script('mapstraction-openlayers', mapasdevista_get_baseurl() . '/js/mxn/mxn.openlayers.core-min.js');
    }
    
    if (isset($_GET['page']) && $_GET['page'] === "mapasdevista_theme_page") {
        
        wp_enqueue_script('jcolorpicker', mapasdevista_get_baseurl() . '/admin/colorpicker/js/colorpicker.js', array('jquery') );
        wp_enqueue_style('colorpicker', mapasdevista_get_baseurl() . '/admin/colorpicker/css/colorpicker.css' );
        wp_enqueue_script('mapasdevista_theme_options', mapasdevista_get_baseurl() . '/admin/mapasdevista_theme_options.js', array('jquery', 'jcolorpicker') );
    
    }

    if($pagenow === "post.php" || $pagenow === "post-new.php") {
        wp_enqueue_script('metabox', mapasdevista_get_baseurl() . '/admin/metabox.js' );
    } elseif(isset($_GET['page']) && $_GET['page'] === 'mapasdevista_pins_page') {
        wp_enqueue_script('metabox', mapasdevista_get_baseurl() . '/admin/pins.js' );
    }


    wp_enqueue_style('mapasdevista-admin', get_bloginfo('template_directory') . '/admin/admin.css');
}

/* Page Template redirect */
add_action('template_redirect', 'mapasdevista_page_template_redirect');

function mapasdevista_page_template_redirect() {

    if (is_page()) {
        $page = get_queried_object();
        if (get_post_meta($page->ID, '_mapasdevista', true)) {
            include('template/main-template.php');
            exit;
        }
    }
}

/**************************/


function mapasdevista_get_template($file, $context = null) {
    
    if (preg_match('|/wp-content/themes/|', __FILE__)) {
        get_template_part($file, $context);
    } else {
        $f = is_null($context) ? $file : $file . '-'. $context ;
        $file = $file . '.php';
        $f = $f . '.php';
        if (
            file_exists(TEMPLATEPATH . '/' . $f) ||
            file_exists(STYLESHEETPATH . '/' . $f) ||
            file_exists(TEMPLATEPATH . '/' . $file) ||
            file_exists(STYLESHEETPATH . '/' . $file) 
            ) {
            get_template_part($file, $context);
        } else {
            include $f;
        }
            
    }
    
}
function mapasdevista_get_baseurl() {
    
    if (preg_match('|/wp-content/themes/|', __FILE__))
        return get_bloginfo('stylesheet_directory') . '/';
    else
        return plugins_url('/', __FILE__);
}

function mapasdevista_get_maps() {

    global $wpdb;
    $maps = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '_mapasdevista'");
    $r = array();
    foreach ($maps as $m) {

        if (!is_serialized($m->meta_value))
            continue;

        $mapinfo = unserialize($m->meta_value);
        $mapinfo['page_id'] = $m->post_id;
        $r[$m->post_id] = $mapinfo;

    }

    return $r;
}

if ( ! function_exists( 'mapasdevista_comment' ) ) :

function mapasdevista_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'twentyten' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'twentyten' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

// IMAGES
function mapasdevista_get_image($name) {
    return mapasdevista_get_baseurl() . '/img/' . $name;
}

function mapasdevista_image($name, $params = null) {
    $extra = '';

    if(is_array($params)) {
        foreach($params as $param=>$value){
            $extra.= " $param=\"$value\" ";		
        }
    }

    echo '<img src="', mapasdevista_get_image($name), '" ', $extra ,' />';
}

function mapasdevista_create_homepage_map($args) {

    /*
    if (get_option('mapasdevista_created_homepage'))
        return __('You have done this before...', 'mapasdevista');
    */
    
    $params = wp_parse_args(
        $args,
        array(
            'name' => __('Home Page Map', 'mapasdevista'),
            'api' => 'openlayers',
            'type' => 'road',
            'coord' => array(
                'lat' => '-13.888513111069498',
                'lng' => '-56.42951505224626'
            ),
            'zoom' => '4',
            'post_types' => array('post'),
            'filters' => array('new'),
            'taxonomies' => array('category')
        )
    );
    
    $page = array(
        'post_title' => 'Home Page',
        'post_content' => __('Page automatically created by Mapas de Vista as a placeholder for your map.', 'mapasdevista'),
        'post_status' => 'publish',
        'post_type' => 'page'
    );
    
    $page_id = wp_insert_post($page);
    
    if ($page_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_id);
        update_option('page_for_posts', 0);
        
        update_post_meta($page_id, '_mapasdevista', $params);
        
        update_option('mapasdevista_created_homepage', true);
        
        return true;
        
    } else {
        return $page_id;
    }    
    
    
    

}
