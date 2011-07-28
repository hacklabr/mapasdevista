<?php $images = get_children( array( 
                        'post_parent' => $post->ID,
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                        'numberposts' => 999 ) ); ?>

<?php if ( $images ) : ?>
    
    <?php the_excerpt(); ?>

    <section id="gallery" class="clearfix">

        <?php foreach( $images as $image) : ?>

            <figure class="gallery-thumb">
                <?php echo wp_get_attachment_image($image->ID); ?>
            </figure>

        <?php endforeach; ?>
        
    </section>

<?php endif; ?>