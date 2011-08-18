<section id="entry-content" class="clearfix">

    <?php the_content(); ?>
    <?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
    
    <?php comments_template(); ?>

</section>