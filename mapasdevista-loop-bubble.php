<div class="hide">
<?php $posts = mapasdevista_get_posts(get_the_ID(), $mapinfo); ?>
<?php while($posts->have_posts()): $posts->the_post(); ?>
<div id="balloon_<?php the_ID(); ?>" class="result clearfix">
    <div class="balloon">
        
        <div class="content">
            <p class="metadata bottom">
                <span class="date"><?php the_time( get_option('date_format') ); ?></span>
            </p>
            <h1 class="bottom"><a class="js-link-to-post" id="balloon-post-link-<?php the_ID(); ?>" href="<?php the_permalink(); ?>" onClick="mapasdevista.linkToPost(this); return false;"><?php the_title(); ?></a></h1>
            <?php mapasdevista_get_template( 'mapasdevista-bubble', get_post_format() ); ?>
        </div>
    </div>
</div>
<?php endwhile; ?>
</div>