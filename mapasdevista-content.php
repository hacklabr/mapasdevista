<section id="entry-content" class="clearfix">

    <?php the_content(); ?>

</section>

<footer class="entry-meta">
    <?php
        $categories_list = get_the_category_list( __( ', ', 'mapasdevista' ) );
        $tag_list = get_the_tag_list( '', __( ', ', 'mapasdevista' ) );
    ?>
    <!--
    <p>
        <?php if($categories_list) : ?>
            <?php _e("Categories: ", "mapasdevista"); echo $categories_list; ?>
        <?php endif; ?>

        <?php if($tag_list) : ?>
            <br/><?php _e("Tags: ", "mapasdevista"); echo $tag_list; ?>
        <?php endif; ?>
    </p>
    -->
</footer>

<?php comments_template(); ?>
