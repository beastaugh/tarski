<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die('This page should not be loaded directly.');

if (post_password_required()) return;

if (have_comments() || comments_open()) { ?>
    <div id="comments-header">
        <div class="clearfix">
            <h2 class="title"><?php comments_number(__('No comments', 'tarski'), __('1 comment', 'tarski'), __('% comments', 'tarski')); ?></h2>
        <?php if (comments_open()) { ?>
              <p class="comments-feed"><a href="<?php echo get_post_comments_feed_link(); ?>"><?php _e('Comments feed for this article', 'tarski'); ?></a></p>
          <?php } ?>
          </div>
          <?php if (pings_open()) { ?>
        <p class="trackback-link"><?php printf(__('Trackback link: %s', 'tarski'),
            '<a href="' . get_trackback_url() . '">' . urldecode(get_trackback_url()) . '</a>') .'</p>'; ?></p>
        <?php } ?>
    </div>
    
    <?php if (have_comments()) { ?>
        <ol id="comments" class="clearfix">
            <?php wp_list_comments(array('style' => 'ol', 'walker' => new TarskiCommentWalker)); ?>
        </ol>
        
        <?php $page_links = paginate_comments_links(array(
            'type' => 'array', 'echo' => false,
            'prev_text' => __('&lsaquo; Previous', 'tarski'),
            'next_text' => __('Next &rsaquo;', 'tarski')));
        
        if ($page_links) echo '<p id="comment-paging">' . join(' &middot; ', $page_links) . '</p>';
    }
    
    if (comments_open()) { ?>
        <div id="respond">
            <?php if (get_option('comment_registration') && !is_user_logged_in()) {  // if registration is mandatory and user not logged in ?>
                <p class="login-required"><em><?php printf(
                    __('You must be %s to post a comment.', 'tarski'),
                    '<a href="' . wp_login_url(get_permalink()) . '">' . __('logged in', 'tarski') . '</a>'
                ); ?></em></p>
                
                <?php } else { // if registration is not mandatory or the user is logged in ?>
                    <form action="<?php echo site_url('wp-comments-post.php'); ?>" method="post" id="commentform">
                        <div id="respond-header" class="clearfix">
                            <h2 class="title"><?php comment_form_title(__('Reply', 'tarski'), __('Reply to %s', 'tarski')); ?></h2>
                            <p class="cancel-reply"><?php cancel_comment_reply_link(__('Click here to cancel your reply', 'tarski')); ?></p>
                        </div>
                    <?php if (is_user_logged_in()) { // if user is logged in ?>
                        <p class="logged-in"><?php printf(__('Logged in as %1$s. %2$s', 'tarski'),
                            '<a href="' . admin_url('profile.php') . '">' . $user_identity . '</a>',
                            '<a href="'. wp_logout_url(get_permalink()) . '">' . __('Log out?', 'tarski') . '</a>'); ?></p>
                    <?php } else { // if user is not logged in - name, email and website fields ?>
                        <div class="response-details clearfix">
                            <?php comment_text_field('author', __('Name %s', 'tarski'), esc_attr($comment_author), $req); ?>
                            <?php comment_text_field('email', __('Email %s', 'tarski'), esc_attr($comment_author_email), $req, 20, 'email'); ?>
                            <?php comment_text_field('url', __('Website', 'tarski'), esc_attr($comment_author_url), false, 20, 'url'); ?>
                        </div>
                    <?php } // textarea etc. start here ?>
                    
                    <div class="response textarea-wrap">
                        <label for="comment"><?php _e('Your comment','tarski'); ?></label>
                        <textarea name="comment" id="comment" cols="60" rows="10" aria-required="true" aria-multiline="true"></textarea>
                        <?php comment_id_fields(); ?>
                    </div>
                    
                    <p class="submit-wrap"><input class="submit" name="submit" type="submit" id="submit" value="<?php _e('Submit Comment', 'tarski'); ?>"></p>
                    
                    <div class="response-extras">
                        <?php do_action('comment_form', get_the_ID()); ?>
                    </div>
                </form>
            <?php } // end registration check ?>
        </div>
    <?php } else { ?>
        <p id="comments-closed"><em><?php _e('Comments are now closed.', 'tarski'); ?></em></p>
    <?php }
} ?>
