<?php
/* In this file we loop thourgh the posts that will be displayed in this map in order to create two sets fo divs
 * 1. results -> The posts as they show up in the right "results" box
 * 2. balloons -> the posts as they show in the info balloons of the markers
 */ 
 
    if ($mapinfo['api'] == 'image') {
        
        $postsArgs = array(
                'numberposts'     => -1,
                'orderby'         => 'post_date',
                'order'           => 'DESC',
                'meta_key'        => '_mpv_in_img_map',
                'meta_value'      => get_the_ID(),
                'post_type'       => $mapinfo['post_types'],
                'ignore_sticky_posts' => true
            );
            

    } else {
        
        $postsArgs = array(
                    'numberposts'     => -1,
                    'orderby'         => 'post_date',
                    'order'           => 'DESC',
                    'meta_key'        => '_mpv_inmap',
                    'meta_value'      => get_the_ID(),
                    'post_type'       => $mapinfo['post_types'],
                    'ignore_sticky_posts' => true
                );
    }

    if (isset($_GET['search']) && $_GET['search'] != '')
        $postsArgs['s'] = $_GET['search'];

    
    ?>

    <?php $posts = new WP_Query($postsArgs);  ?>
        
        <div id="results" class="clearfix">
            <h1><?php _e('Results', 'mapasdevista'); ?> [<span id="filter_total"><?php echo $posts->found_posts; ?></span>]</h1>
            <div class="clear"></div>
            <?php if ($posts->have_posts()): ?>
            
                <?php while($posts->have_posts()): $posts->the_post(); ?>
                
                    <?php mapasdevista_get_template('mapasdevista-loop','filter'); ?>
                    <?php mapasdevista_get_template('mapasdevista-loop', 'bubble'); ?>
                
                <?php endwhile; ?>
            
            <?php else: ?>
                <?php _e('No posts found', 'mapasdevista'); ?>
            <?php endif; ?>
        </div>
