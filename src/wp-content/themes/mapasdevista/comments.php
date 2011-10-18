<?php if(!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!'); ?>
<?php if(post_password_required()) return; ?>
<?php
// add a microid to all the comments
function comment_add_microid($classes)
{
  $c_email = get_comment_author_email();
  $c_url = get_comment_author_url();
  if (!empty($c_email) && !empty($c_url)) {
    $microid = 'microid-mailto+http:sha1:' . sha1(sha1('mailto:'.$c_email).sha1($c_url));
    $classes[] = $microid;
  }
  return $classes;  
}

add_filter('comment_class','comment_add_microid');
?>

<div id="comments"> 
    <?php if ('open' == $post->comment_status) : ?>
        <h3><?php _e('Comments', 'mapasdevista'); ?></h3>

        <h4><?php comments_number(__('No comments', 'mapasdevista'), __('1 comment','mapasdevista'), __('%s comments','mapasdevista') );?> | <a href="#respond" title="Comente"><?php _e('Leave a comment &raquo;', 'mapasdevista'); ?></a></h4>
        <ul class="commentlist" id="singlecomments">
            <?php wp_list_comments('callback=mapasdevista_comment'); ?>
        </ul>
        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
            <div class="navigation">
                <div class="alignleft"><?php previous_comments_link( __( '&laquo; Older Comments', 'mapasdevista' ) ); ?></div>
                <div class="alignright"><?php next_comments_link( __( 'Newer Comments &raquo;', 'mapasdevista' ) ); ?></div>
            </div><!-- .navigation -->
        <?php endif; // check for comment navigation ?>
            
    <?php endif; ?>
    
    <!--show the form-->
    <?php if('open' == $post->comment_status) : ?>
    <div id="respond" class="clearfix">
        <h3><?php _e('Leave a comment', 'mapasdevista'); ?></h3>
        <?php if(get_option('comment_registration') && !$user_ID) : ?>
        <p>Você precisa estar <a href="<?php print get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logado</a> para publicar um comentário.</p>
        <?php else : ?>
        <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform" class="clearfix">
            <?php comment_id_fields(); ?>                      
            <textarea name="comment" id="comment" tabindex="1" onfocus="if (this.value == 'Insira seu comentário aqui.') this.value = '';" onblur="if (this.value == '') {this.value = 'Insira seu comentário aqui.';}">Insira seu comentário aqui.</textarea>
            <?php if($user_ID) : ?>
                <p>Conectado como <a href="<?php print get_option('siteurl'); ?>/wp-admin/profile.php"><?php print $user_identity; ?></a>. <a href="<?php print get_option('siteurl'); ?>/wp-login.php?action=logout" title="Logout">Logout &raquo;</a></p>
            <?php else : ?>                
                <input type="text" name="author" id="author" onfocus="if (this.value == 'nome') this.value = '';" onblur="if (this.value == '') {this.value = 'nome';}"  value="nome" tabindex="2" />
                <input type="text" name="email" id="email" onfocus="if (this.value == 'email') this.value = '';" onblur="if (this.value == '') {this.value = 'email';}" value="email" tabindex="3" />
                <input type="text" name="url" id="url" value="http://" tabindex="4" />					
            <?php endif; ?>           
            <?php cancel_comment_reply_link('cancelar'); ?><input type="submit" name="submit" id="submit" value="<?php _e('comment', 'mapasdevista'); ?>" />
            <?php if(get_option("comment_moderation") == "1") : ?>
            <?php _e('All comments need to be approved', 'mapasdevista'); ?>
            <?php endif; ?>
            <?php do_action('comment_form', $post->ID); ?>
        </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
