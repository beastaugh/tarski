<?php global $trackbackLink;

// Do not delete these lines
if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');
	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
?>
	<p><?php _e('This post is password protected. Enter the password to view comments.', 'tarski'); ?><p>
<?php
		return;
	}
}

/* This variable is for alternating comment background */
$oddcomment = 'alt';

?>

<?php if ($comments) : ?>
<div id="comments">
<?php if ('open' == $post-> comment_status) : ?>

	<div class="meta">
		<div class="secondary"><h2 class="title"><?php comments_number(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski')); ?></h2></div>
		<div class="primary"><p class="comments-feed"><?php comments_rss_link(__('Comments feed for this article', 'tarski')); ?></p></div>
		<?php if(pings_open()) { ?><div id="trackback-link"><p class="secondary"><?php _e('Trackback link', 'tarski'); ?></p><p class="primary"><a href="<?php echo $trackbackLink; ?>"><?php echo $trackbackLink; ?></a></p></div><?php } ?>
	</div> <!-- close comments header div -->

<?php else : // comments are closed ?>

	<div id="meta">
		<h2 class="title"><?php comments_number(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski')); ?></h2>
	</div> <!-- /comments header -->

<?php endif; ?>










<?php foreach ($comments as $comment) : ?>
	<?php if ($comment->comment_type == "trackback" || $comment->comment_type == "pingback" || ereg("<pingback />", $comment->comment_content) || ereg("<trackback />", $comment->comment_content)) { ?>
		
	<div class="trackback" id="comment-<?php comment_ID() ?>">
		<div class="secondary">
			<p><a href="#comment-<?php comment_ID() ?>" title="<?php _e('Permalink to this comment', 'tarski'); ?>"><?php comment_date(); _e(' at ', 'tarski'); comment_time(); ?></a></p>
		</div>
		<div class="primary">
			<p>
			<?php preg_match("@^<strong>(.*?)</strong>@", $comment->comment_content, $matches); if($matches[1]) { ?>
				<?php comment_type(__('Comment', 'tarski'), __('Trackback', 'tarski'), __('Pingback', 'tarski')); echo ' ' . __(' from', 'tarski'); ?> <strong><?php comment_author(); ?></strong><?php echo " - <a href=\""; comment_author_url(); echo "\">" . $matches[1] . "</a>"; ?>
			<?php } else { ?>
				<?php comment_type(__('Comment', 'tarski'), __('Trackback', 'tarski'), __('Pingback', 'tarski')); echo ' ' . __(' from', 'tarski'); ?> <strong><?php echo "<a href=\""; comment_author_url(); echo "\">"; comment_author(); echo "</a>"; ?></strong>
			<?php } ?>
			<?php edit_comment_link(__('edit', 'tarski'), '(', ')'); ?></p>
		</div>
	</div> <!-- /trackback -->
	<?php } // end if trackback ?>
<?php endforeach; /* end for each comment */ ?>


<?php foreach ($comments as $comment) : $comment_count++; ?>
	<?php if ($comment->comment_type != "trackback" && $comment->comment_type != "pingback" && !ereg("<pingback />", $comment->comment_content) && !ereg("<trackback />", $comment->comment_content)) { ?>
		
	<div class="comment<?php
	// Style differently if comment author is blog author
	if ($comment->comment_author_email == get_the_author_email()) { echo ' author-comment'; }
	if ($comment->comment_approved == '0') { echo ' moderated'; } ?>" id="comment-<?php comment_ID() ?>">
		<?php if ($comment->comment_approved == '0') { ?>
		<p class="primary-span"><strong><?php _e('Your comment is awaiting moderation.', 'tarski'); ?></strong></p>
		<?php } ?>
		<div class="secondary">
			<p class="comment-permalink"><a href="#comment-<?php comment_ID(); ?>" title="<?php _e('Permalink to this comment', 'tarski'); ?>"><?php comment_date(); _e(' at ', 'tarski'); comment_time() ?></a></p>
			<p class="comment-author"><strong><?php comment_author_link(); ?></strong></p>
			<?php edit_comment_link(__('edit', 'tarski'), '<p class="comment-permalink">(', ')</p>'); ?> 
		</div> <!-- /comment meta -->
		
		<div class="primary content">
			<?php
			if (function_exists('gravatar')) {
				if(get_comment_author_url()) {
					echo "<a href=\"" . get_comment_author_url() . "\">";
				}
				echo "<img class=\"gravatar\" src=\"";
				gravatar($comment->comment_author_email);
				echo "\" alt=\"\" />";
				if(get_comment_author_url()) {
					echo "</a>";
				}
			}
			?>
			<?php comment_text() ?>
		</div> <!-- /comment content -->
	</div> <!-- /comment -->
	<?php } // end if not trackback ?>
<?php endforeach; /* end for each comment */ ?>






</div>
<?php else : // this is displayed if there are no comments so far ?>

<?php if ('open' == $post-> comment_status) : ?>
<div id="comments">
	<div class="meta">
		<div class="secondary"><h2 class="title"><?php comments_number(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski')); ?></h2></div>
		<div class="primary"><p class="comments-feed"><?php comments_rss_link('Comments feed for this article'); ?></p></div>
		<?php if(pings_open()) { ?><div id="trackback-link"><p class="secondary"><?php _e('Trackback link', 'tarski'); ?></p><p class="primary"><a href="<?php echo $trackbackLink; ?>"><?php echo $trackbackLink; ?></a></p></div><?php } ?>
	</div> <!-- /comment content -->
</div> <!-- /comment -->
<?php else : // comments are closed ?>

<?php endif; ?>

<?php endif; ?>

<?php if ('open' == $post-> comment_status) : ?>

<div id="respond">

<?php // if registration is mandatory
	if ( get_option('comment_registration') && !$user_ID ) : ?>
	<div class="content">
		<p><em><?php _e('You must be ', 'tarski'); ?><a href="<?php echo get_option('siteurl'); echo '/wp-login.php?redirect_to='; the_permalink(); ?>"><?php _e('logged in', 'tarski'); ?></a><?php _e(' to post a comment.', 'tarski'); ?></em></p>
	</div>
</div>
<?php else : ?>

		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform"><fieldset>

<?php // if user is logged in
	if ( $user_ID ) : ?>

		<div id="info-input" class="content">
			<p class="userinfo"><?php _e('You are logged in as ', 'tarski'); ?><a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>.</p>
			<p><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account', 'tarski') ?>"><?php _e('Logout &raquo;', 'tarski'); ?></a></p>
			<?php if(function_exists('show_subscription_checkbox')) { show_subscription_checkbox(); } ?>
		</div> <!-- /info fields -->

<?php // if user is not logged in - name, email and website fields
	else : ?>

			<div id="info-input" class="content">
				<label for="author"><?php _e('Name', 'tarski'); ?><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" /></label>
				<label for="email"><?php _e('Email', 'tarski'); ?><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" /></label>
				<label for="url"><?php _e('Website', 'tarski'); ?><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" /></label>
				<?php if(function_exists('show_subscription_checkbox')) { show_subscription_checkbox(); } ?>
			</div> <!-- /info fields -->


<?php // actual comment form
endif; ?>
			<div id="comment-input">
				<label for="comment"><?php _e('Your comment', 'tarski'); ?></label>
				<textarea name="comment" id="comment" cols="60" rows="12" tabindex="4"></textarea>
				<input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
				<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
				<?php if (function_exists('live_preview')) { live_preview(); } ?>
				<?php @include(TEMPLATEPATH . '/constants.php'); echo $commentsFormInclude; ?>
			</div>  <!-- /comment input -->
<?php do_action('comment_form', $post->ID); ?>
		</fieldset></form>
	</div> <!-- /comment form -->

<?php endif; // If registration required and not logged in ?>
<?php endif; // if you delete this the sky will fall on your head / O RLY / YA RLY ?>