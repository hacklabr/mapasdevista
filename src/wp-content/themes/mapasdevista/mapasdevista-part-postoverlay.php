<?php //global $post; ?>
<div id="post_<?php the_ID(); ?>" class="result clearfix">
    
    <div class="content">
        <p class="metadata date bottom"><?php the_time( get_option('date_format') ); ?></p>
        <h1 class="bottom"><?php the_title(); ?></h1>
        <p><?php the_content(); ?></p>
        <p class="metadata author"><?php _e('Published by', 'mapasdevista'); ?>
            <?php the_author(); ?>
        </p>
    </div>
    
</div>
