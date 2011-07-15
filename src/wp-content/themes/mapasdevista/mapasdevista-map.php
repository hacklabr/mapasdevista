<?php

global $wp_post_types;

$obj = get_queried_object();

wp_enqueue_script( 'mapasdevista', mapasdevista_get_baseurl() . 'js/front-end.js', array('jquery') );

$mapinfo = get_post_meta($obj->ID, '_mapasdevista', true);


if ($mapinfo['api'] == 'image') {

        $image_src = get_post_meta(get_the_ID(), '_thumbnail_id', true);
        
        $image_src = wp_get_attachment_image_src($image_src);
        $image_src = $image_src[0];

        wp_localize_script( 'mapasdevista', 'mapinfo', array(
        'image_src' => $image_src,
        'api' => $mapinfo['api'],
        'ajaxurl' => admin_url('admin-ajax.php'),
        'page_id' => get_the_ID()

    ) );

} else {

    wp_localize_script( 'mapasdevista', 'mapinfo', array(
        
        'api' => $mapinfo['api'],
        'lat' => $mapinfo['coord']['lat'],
        'lng' => $mapinfo['coord']['lng'],
        'zoom' => $mapinfo['zoom'],
        'type' => $mapinfo['type'],
        'ajaxurl' => admin_url('admin-ajax.php'),
        'page_id' => get_the_ID()

    ) );
}


wp_enqueue_script('mapstraction', get_bloginfo('template_directory') . '/js/mxn/mxn-min.js' );
wp_enqueue_script('mapstraction-core', get_bloginfo('template_directory') . '/js/mxn/mxn.core-min.js');

if ($mapinfo['api'] == 'openlayers') {
    wp_enqueue_script('openlayers', 'http://openlayers.org/api/OpenLayers.js');
    wp_enqueue_script('mapstraction-openlayers', get_bloginfo('template_directory') . '/js/mxn/mxn.openlayers.core-min.js');
} elseif ($mapinfo['api'] == 'googlev3') {
    // api do google maps versao 3 direto TODO: colocar a chave (&key)
    wp_enqueue_script('google-maps-v3', 'http://maps.google.com/maps/api/js?sensor=false');
    wp_enqueue_script('mapstraction-googlev3', get_bloginfo('template_directory') . '/js/mxn/mxn.googlev3.core-min.js');
} elseif ($mapinfo['api'] == 'image') {
    wp_enqueue_script('mapstraction-image', get_bloginfo('template_directory') . '/js/mxn/mxn.image.core.js');
}

?><!DOCTYPE html>
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
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>


<div id="map" style="width:500px; height: 500px; float: left;">

</div>


<?php wp_nav_menu( array( 'container_class' => 'map-menu-top', 'theme_location' => 'mapasdevista_top', 'fallback_cb' => false ) ); ?>
<?php wp_nav_menu( array( 'container_class' => 'map-menu-side', 'theme_location' => 'mapasdevista_side', 'fallback_cb' => false ) ); ?>


<div id="filters">
    
    <?php if (is_array($mapinfo['filters'])): ?>
        
        <?php foreach ($mapinfo['filters'] as $filter): ?>
        
        
        
            <?php if ($filter == 'new') : ?>
                
                <input type="checkbox" name="filter_by_new" id="filter_by_new" value="1" />
                <label for="filter_by_new"><?php _e('Show only new posts', 'mapasdevista'); ?></label>
                
            <?php elseif ($filter == 'post_types') : ?>
                
                <h3><?php _e('Content Types', 'mapasdevista'); ?></h3>
                <ul class="filter-group" id="filter_post_types">                
                    
                    <?php foreach ($mapinfo['post_types'] as $type) : ?>

                        <li>
                        <input type="checkbox" class="post_type-filter-checkbox" name="filter_by_post_type[]" value="<?php echo $type; ?>" id="filter_post_type_<?php echo $type; ?>"> 
                        <label for="filter_post_type_<?php echo $type; ?>">
                        <?php echo $wp_post_types[$type]->label; ?>
                        </label>
                        </li>
                        

                    <?php endforeach; ?>
                
                </ul>
            
            <?php endif; ?>
        
        
        <?php endforeach; ?>
        
    <?php endif; ?>
    
    <?php if (is_array($mapinfo['taxonomies'])): ?>
    
        <?php foreach ($mapinfo['taxonomies'] as $filter): ?>
             
             <?php $taxonomy = get_taxonomy($filter); ?>
             
             <h3><?php echo $taxonomy->label; ?></h3>
             <ul class="filter-group filter-taxonomy" id="filter_taxonomy_<?php echo $filter; ?>">
             
                <?php mapasdevista_taxonomy_checklist($filter); ?>
             
             </ul>
        
        <?php endforeach; ?>
    
    <?php endif; ?>
    <?php
    
    function mapasdevista_taxonomy_checklist($taxonomy, $parent = 0) {
    
        $terms = get_terms($taxonomy, 'hide_empty=0&orderby=name&parent='. $parent);
        
        if (!is_array($terms) || ( is_array($terms) && sizeof($terms) < 1 ) )
            return;
        
        ?>
        
        <?php if ($parent > 0): ?>
        <ul class='children'>
        <?php endif; ?>
        
        <?php foreach ($terms as $term): ?>
        <li>
            
            <input type="checkbox" class="taxonomy-filter-checkbox" value="<?php echo $term->slug; ?>" name="filter_by_<?php echo $taxonomy; ?>[]" id="filter_by_<?php echo $taxonomy; ?>_<?php echo $term->slug; ?>" />
            <label for="filter_by_<?php echo $taxonomy; ?>_<?php echo $term->slug; ?>">
            <?php echo $term->name; ?>
            </label>
            
        </li>
        
        <?php mapasdevista_taxonomy_checklist($taxonomy, $term->term_id); ?>
        
        <?php endforeach; ?>
        
        <?php if ($parent > 0): ?>
        </ul>
        <?php endif; ?>
        
        <?php
        
        
    
    }
    
    ?>
    
</div>


<?php wp_footer(); ?>
</body>
</html>
