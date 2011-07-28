<?php
//global $post;

$format = get_post_format() ? get_post_format() : 'default';
?>


<article id="post_<?php the_ID(); ?>" class="entry <?php echo $format; ?> clearfix">

    <p class="metadata date bottom"><?php the_time( get_option('date_format') ); ?></p>
    <h1 class="bottom"><?php the_title(); ?></h1>
    <p class="metadata author"><?php _e('Published by', 'mapasdevista'); ?>
        <?php the_author(); ?>
    </p>
    
    <div class="entry-content">        
        <?php get_template_part( 'content', get_post_format() ); ?>
    </div>
    
</article>