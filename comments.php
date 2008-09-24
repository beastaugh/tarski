<?php

if('comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die(__('This page should not be loaded directly.', 'tarski'));
}

if(!empty($post->post_password)) { // if there's a password
	if($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) { // and it doesn't match the cookie
		echo '<p>' . __('This post is password protected. Enter the password to view comments.','tarski') . '</p>' . "\n";
		return;
	}
}

if($comments || comments_open()) { ?>
<div id="comments">
	
	<div class="meta clearfix">
		<h2 class="title"><?php comments_number(__('No comments','tarski'), __('1 comment','tarski'), '%'. __(' comments','tarski')); ?></h2>
		<?php if(comments_open()) { ?>
		<p class="comments-feed"><a href="<?php echo get_post_comments_feed_link($post->ID); ?>"><?php _e('Comments feed for this article','tarski'); ?></a></p>
		<?php } ?>
		<?php if(pings_open()) { ?>
		<div id="trackback-link" class="clearfix">
			<div class="secondary"><p><?php _e('Trackback link','tarski'); ?></p></div>
			<div class="primary"><p><a href="<?php trackback_url(); ?>"><?php trackback_url(); ?></a></p></div>
		</div>
		<?php } ?>
	</div> <!-- /comments header -->
	
	<?php if($comments) { ?>
	
		<?php foreach($comments as $comment) { // Run comments loop for track- and pingbacks ?>
		
			<?php if($comment->comment_type == "trackback" || $comment->comment_type == "pingback" || ereg("<pingback />", $comment->comment_content) || ereg("<trackback />", $comment->comment_content)) { ?>

				<div class="trackback clearfix" id="comment-<?php comment_ID() ?>">
					<div class="secondary">
						<p><a href="#comment-<?php comment_ID() ?>" title="<?php _e('Permalink to this comment','tarski'); ?>"><?php printf(__('%1$s at %2$s', 'tarski'), get_comment_date(), get_comment_time()); ?></a></p>
					</div>
					<div class="primary">
						<p>
						<?php preg_match("@^<strong>(.*?)</strong>@", $comment->comment_content, $matches); if($matches[1]) { ?>
							<?php comment_type(__('Comment','tarski'), __('Trackback','tarski'), __('Pingback','tarski')); echo ' ' . __(' from','tarski'); ?> <strong><?php comment_author(); ?></strong><?php echo " - <a href=\""; comment_author_url(); echo "\">" . $matches[1] . "</a>"; ?>
						<?php } else { ?>
							<?php comment_type(__('Comment','tarski'), __('Trackback','tarski'), __('Pingback','tarski')); echo ' ' . __(' from','tarski'); ?> <strong><?php echo "<a href=\""; comment_author_url(); echo "\">"; comment_author(); echo "</a>"; ?></strong>
						<?php } ?>
						<?php edit_comment_link(__('edit','tarski'), '(', ')'); ?></p>
					</div>
				</div> <!-- /trackback -->
		
			<?php } ?>
			
		<?php } $comment_count = 0; foreach($comments as $comment) { // Run comments loop again for normal comments ?>

			<?php if($comment->comment_type != "trackback" && $comment->comment_type != "pingback" && !ereg("<pingback />", $comment->comment_content) && !ereg("<trackback />", $comment->comment_content)) { $comment_count++; ?>

				<div class="comment clearfix<?php
				if ( get_comment_author_email() == get_the_author_email() ) { echo ' author-comment'; }
				if ($comment->comment_approved == '0') { echo ' moderated'; } ?>" id="comment-<?php comment_ID() ?>">
					<?php if ($comment->comment_approved == '0') { ?>
					<p class="primary-span"><strong><?php _e('Your comment is awaiting moderation.','tarski'); ?></strong></p>
					<?php } ?>
					<div class="secondary">
						<p class="comment-permalink"><a href="#comment-<?php comment_ID(); ?>" title="<?php _e('Permalink to this comment','tarski'); ?>"><?php echo tarski_comment_datetime(); ?></a></p>
						<p class="comment-author vcard"><?php echo tarski_comment_author_link(); ?></p>
						<?php echo tarski_avatar(); ?>
						<?php edit_comment_link(__('edit','tarski'), '<p class="comment-edit">(', ')</p>'); ?>
					</div> <!-- /comment meta -->

					<div class="primary content">
						<?php comment_text(); ?>
					</div> <!-- /comment content -->
				</div> <!-- /comment -->
		
			<?php } // end comment type eval ?>
		<?php } // end foreach loop ?>
	<?php } // end if comments loop ?>
</div>

<?php } include_once(TARSKIDISPLAY . '/respond.php'); ?>
