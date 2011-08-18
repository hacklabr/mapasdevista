<section id="entry-content" class="clearfix">

    <?php the_content(); ?>

</section>

<footer class="entry-meta">
    <?php
        $categories_list = get_the_category_list( __( ', ', 'mapasdevista' ) );
        $tag_list = get_the_tag_list( '', __( ', ', 'mapasdevista' ) );
    ?>

    <?php if($categories_list) : ?>
        <p class="bottom"><?php _e("Categories: ", "mapasdevista"); echo $categories_list; ?></p>
    <?php endif; ?>

    <?php if($tag_list) : ?>
        <p><?php _e("Tags: ", "mapasdevista"); echo $tag_list; ?></p>
    <?php endif; ?>

</footer>

<?php comments_template(); ?>