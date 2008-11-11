<div id="respond">
<?php if(get_option('comment_registration') && !$user_ID) {  // if registration is mandatory and user not logged in ?>
	
	<div class="content">
		<p><em><?php printf(
			__('You must be %s to post a comment.', 'tarski'),
			'<a href="' . wp_login_url(get_permalink()) . '">' . __('logged in', 'tarski') . '</a>'
		); ?></em></p>
	</div>

<?php } else { // if registration is not mandatory or the user is logged in ?>
	
	<form action="<?php echo site_url('wp-comments-post.php'); ?>" method="post" id="commentform">
		<div id="respond-header">
			<h2 class="title"><?php comment_form_title(__('Reply', 'tarski'), __('Reply to %s', 'tarski')); ?></h2>
			<p class="cancel"><?php cancel_comment_reply_link(); ?></p>
		</div>
		
	<?php if($user_ID) { // if user is logged in ?>
		
		<p class="logged-in"><?php printf(__('Logged in as %1$s. %2$s', 'tarski'),
			'<a href="' . admin_url('profile.php') . '">' . $user_identity . '</a>',
			'<a href="'. wp_logout_url(get_permalink()) . '">' . __('Log out?', 'tarski') . '</a>'); ?></p>

	<?php } else { // if user is not logged in - name, email and website fields ?>
		
		<div class="details">
			<?php comment_text_field('author', __('Name %s', 'tarski'), $comment_author, $req); ?>
			<?php comment_text_field('email', __('Email %s', 'tarski'), $comment_author_email, $req); ?>
			<?php comment_text_field('url', __('Website', 'tarski'), $comment_author_url); ?>
		</div>
		
	<?php } // textarea etc. start here ?>
		
		<div class="textarea-wrap">
			<label for="comment"><?php _e('Your comment','tarski'); ?></label>
			<textarea name="comment" id="comment" cols="60" rows="10"></textarea>
			<input name="submit" type="submit" id="submit" value="<?php _e('Submit Comment','tarski'); ?>" />
			<?php comment_id_fields(); ?>
		</div>
		
		<?php do_action('comment_form', $post->ID); ?>
	</form>

<?php } // end registration check ?>
</div>
