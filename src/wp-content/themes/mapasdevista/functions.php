<?php


// includes
include('admin/maps.php');
include('admin/pins.php');
include('admin/theme.php');
include('admin/metabox.php');
include('mapasdevista-get-posts.php');
include('includes/image.php');


/* APAGAR ANTES DE IR PARA PRODUÇÃO!!!!!! */

add_action( 'init', 'create_book_taxonomies', 0 );

function create_book_taxonomies()  {
  $labels = array(
    'name' => _x( 'Genres', 'taxonomy general name' ),
    'singular_name' => _x( 'Genre', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Genres' ),
    'all_items' => __( 'All Genres' ),
    'parent_item' => __( 'Parent Genre' ),
    'parent_item_colon' => __( 'Parent Genre:' ),
    'edit_item' => __( 'Edit Genre' ), 
    'update_item' => __( 'Update Genre' ),
    'add_new_item' => __( 'Add New Genre' ),
    'new_item_name' => __( 'New Genre Name' ),
    'menu_name' => __( 'Genre' ),
  ); 	

  register_taxonomy('genre',array('book'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'genre' ),
  ));

  $labels = array(
    'name' => _x( 'Writers', 'taxonomy general name' ),
    'singular_name' => _x( 'Writer', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Writers' ),
    'popular_items' => __( 'Popular Writers' ),
    'all_items' => __( 'All Writers' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Writer' ), 
    'update_item' => __( 'Update Writer' ),
    'add_new_item' => __( 'Add New Writer' ),
    'new_item_name' => __( 'New Writer Name' ),
    'separate_items_with_commas' => __( 'Separate writers with commas' ),
    'add_or_remove_items' => __( 'Add or remove writers' ),
    'choose_from_most_used' => __( 'Choose from the most used writers' ),
    'menu_name' => __( 'Writers' ),
  ); 

  register_taxonomy('writer','book',array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'writer' ),
  ));
}

/**********************************************/

add_action( 'after_setup_theme', 'mapasdevista_setup' );
if ( ! function_exists( 'mapasdevista_setup' ) ):

    function mapasdevista_setup() {

        // Post Format support. You can also use the legacy "gallery" or "asides" (note the plural) categories.
        add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );

        // This theme uses post thumbnails
        add_theme_support( 'post-thumbnails' );

        // Add default posts and comments RSS feed links to head
        add_theme_support( 'automatic-feed-links' );

        // Make theme available for translation
        // Translations can be filed in the /languages/ directory
        load_theme_textdomain( 'mapasdevista', TEMPLATEPATH . '/languages' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( array(
            'mapasdevista_top' => __( 'Map Menu (top)', 'mapasdevista' ),
            'mapasdevista_side' => __( 'Map Menu (side)', 'mapasdevista' )
        ) );

	    define( 'HEADER_IMAGE_WIDTH', apply_filters( 'mapasdevista_header_image_width', 940 ) );
	    define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'mapasdevista_header_image_height', 198 ) );

        // We'll be using post thumbnails for custom header images on posts and pages.
        // We want them to be 940 pixels wide by 198 pixels tall.
        // Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
        set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

    }

endif;



add_action( 'admin_menu', 'mapasdevista_admin_menu' );

function mapasdevista_admin_menu() {

    add_submenu_page('mapasdevista_maps', __('Maps', 'mapasdevista'), __('Maps', 'mapasdevista'), 'manage_maps', 'mapasdevista_maps', 'mapasdevista_maps_page');
    add_menu_page(__('Maps of view', 'mapasdevista'), __('Maps of view', 'mapasdevista'), 'manage_maps', 'mapasdevista_maps', 'mapasdevista_maps_page',null,30);
    add_submenu_page('mapasdevista_maps', __('Pins', 'tnb'), __('Pins', 'tnb'), 'manage_maps', 'mapasdevista_pins_page', 'mapasdevista_pins_page');
    add_submenu_page('mapasdevista_maps', __('Theme Options', 'tnb'), __('Theme Options', 'tnb'), 'manage_maps', 'mapasdevista_theme_page', 'mapasdevista_theme_page');

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
        // api do google maps versao 3 direto TODO: colocar a chave (&key)
        wp_enqueue_script('google-maps-v3', 'http://maps.google.com/maps/api/js?sensor=false');

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

/* Page template redirect */

add_action('template_redirect', 'mapasdevista_page_template_redirect');

function mapasdevista_page_template_redirect() {

    if (is_page()) {
        $page = get_queried_object();
        if (get_post_meta($page->ID, '_mapasdevista', true)) {
            include(mapasdevista_get_template('mapasdevista-map.php'));
            exit;
        }
    }
}

/**************************/

// this is useless right now, but is going to be usefull if we decide to use mapasdevista as a plugin
function mapasdevista_get_template($file) {
    return TEMPLATEPATH . '/' . $file;
}
function mapasdevista_get_baseurl() {
    return get_bloginfo('stylesheet_directory') . '/';
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

/** TODO: DAQUI PRA BAIXO LIMPAR CONFORME NECESSARIO **/


/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function twentyten_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Twenty Ten 1.0
 * @return string "Continue Reading" link
 */
function twentyten_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyten_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function twentyten_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyten_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyten_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function twentyten_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyten_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css. This is just
 * a simple filter call that tells WordPress to not use the default styles.
 *
 * @since Twenty Ten 1.2
 */
//add_filter( 'use_default_gallery_style', '__return_false' );


if ( ! function_exists( 'mapasdevista_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyten_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
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
