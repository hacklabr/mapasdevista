<?php $posts = mapasdevista_get_posts(get_the_ID(), $mapinfo); ?>
<div id="results" class="clearfix">
    <h1><?php _e('Results', 'mapasdevista'); ?> [<span id="filter_total"><?php echo $posts->post_count; //->found_posts;    ?></span>]</h1>
    <div class="clear"></div>
    <?php while($posts->have_posts()): $posts->the_post(); ?>
        <div id="result_<?php the_ID(); ?>" class="result clearfix">
            <div class="pin"><?php the_pin(); ?></div>
            <div class="content">
                <p class="metadata date bottom"><?php the_time(get_option('date_format')); ?></p>
                <!-- the permalink to the post must have the js-link-to-post class. With this, mapasdevista will open the post over the map. 
                It also have to have an id attribute with the ID of th target post. the id can be anything as long as the post ID is the only numeric part of it. -->
                <h1 class="bottom"><a class="js-link-to-bubble" id="post-link-<?php the_ID(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                <p class="metadata author"><?php _e('Published by', 'mapasdevista'); ?>
                    <a class="js-filter-by-author-link" href="<?php echo get_author_posts_url(get_the_ID()); ?>" id="author-link-<?php the_author_ID(); ?>" title="<?php esc_attr(the_author()); ?>"><?php the_author(); ?></a>
                </p>
            </div>
        </div>
    <?php endwhile; ?>
</div>
