<?php if(comments_open()) { ?>

<div id="respond">
	
<?php if(get_option('comment_registration') && !$user_ID) {  // if registration is mandatory and user not logged in ?>
	
	<div class="content">
		<p><em><?php printf(
			__('You must be %s to post a comment.', 'tarski'),
			'<a href="' . wp_login_url(get_permalink()) . '">' . __('logged in', 'tarski') . '</a>'
		); ?></em></p>
	</div>
</div>

<?php } else { // if registration is not mandatory ?>
	
	<form action="<?php echo site_url('wp-comments-post.php'); ?>" method="post" id="commentform"><fieldset>
		<h2><?php comment_form_title(__('Reply', 'tarski'), __('Reply to %s', 'tarski')); ?></h2>
		<p><?php cancel_comment_reply_link(); ?></p>
		
	<?php if($user_ID) { // if user is logged in ?>
		
		<div id="info-input" class="secondary content">
			<p class="userinfo"><?php printf(
				__('You are logged in as %s.', 'tarski'),
				'<a href="' . admin_url('profile.php') . '">' . $user_identity . '</a>'
			); ?></p>
			
			<p><a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'tarski') ?>"><?php _e('Logout &raquo;', 'tarski'); ?></a></p>
		</div> <!-- /info fields -->

	<?php } else { // if user is not logged in - name, email and website fields ?>
		
		<div id="info-input" class="secondary content">
			<label for="author" class="required"><?php _e('Name','tarski'); ?> <span class="req-notice"><?php _e('(required)','tarski'); ?></span><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" /></label>
			<label for="email" class="required"><?php _e('Email','tarski'); ?> <span class="req-notice"><?php _e('(required, not displayed)','tarski'); ?></span><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" /></label>
			<label for="url"><?php _e('Website','tarski'); ?><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" /></label>
		</div> <!-- /info fields -->

	<?php } // textarea etc. start here ?>
	
		<div id="comment-input" class="primary">
			<label for="comment"><?php _e('Your comment','tarski'); ?></label>
			<textarea name="comment" id="comment" cols="60" rows="12"></textarea>
			<input name="submit" type="submit" id="submit" value="<?php _e('Submit Comment','tarski'); ?>" />
			<?php comment_id_fields(); ?>
		</div>  <!-- /comment input -->
	<?php do_action('comment_form', $post->ID); ?>
	</fieldset></form>
</div> <!-- /comment form -->
<?php } // end registration check ?>
<?php } // end form conditional ?>
