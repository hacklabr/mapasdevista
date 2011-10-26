<?php // AS IMAGENS ESTÃO COM POSIÇÃO ABSOLUTA PARA FAZER O SLIDESHOW ?>

<?php $images = get_children( array( 
                        'post_parent' => $post->ID,
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                        'numberposts' => -1 ) ); ?>

<?php if ( $images ) : ?>
    
    <section id="entry-gallery-<?php the_ID(); ?>" class="clearfix slideshow entry-gallery">

        <?php foreach( $images as $image) : ?>

            
            <?php echo wp_get_attachment_image($image->ID, 'mapasdevista-thumbnail'); ?>
            

        <?php endforeach; ?>
        
    </section>
    
<?php endif; ?>
