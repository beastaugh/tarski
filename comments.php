<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

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
        <?php comment_form(tarski_comment_form()); ?>
    <?php } else { ?>
        <p id="comments-closed"><em><?php _e('Comments are now closed.', 'tarski'); ?></em></p>
    <?php }
} ?>
