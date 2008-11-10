<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */
?><div id="respond">
<?php if (get_option('comment_registration') && !$user_ID) {  // if registration is mandatory and user not logged in ?>
	
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
		
	<?php if($user_ID) { // if user is logged in ?>
		
		<p class="logged-in"><?php printf(__('Logged in as %1$s. %2$s', 'tarski'),
			'<a href="' . admin_url('profile.php') . '">' . $user_identity . '</a>',
			'<a href="'. wp_logout_url(get_permalink()) . '">' . __('Log out?', 'tarski') . '</a>'); ?></p>

	<?php } else { // if user is not logged in - name, email and website fields ?>
		
		<div class="response-details clearfix">
			<?php comment_text_field('author', __('Name %s', 'tarski'), $comment_author, $req); ?>
			<?php comment_text_field('email', __('Email %s', 'tarski'), $comment_author_email, $req); ?>
			<?php comment_text_field('url', __('Website', 'tarski'), $comment_author_url); ?>
		</div>
		
	<?php } // textarea etc. start here ?>
		
		<div class="response textarea-wrap">
			<label for="comment"><?php _e('Your comment','tarski'); ?></label>
			<textarea name="comment" id="comment" cols="60" rows="10"></textarea>
			<?php comment_id_fields(); ?>
		</div>
		
		<p class="submit-wrap"><input class="submit" name="submit" type="submit" id="submit" value="<?php _e('Submit Comment', 'tarski'); ?>" /></p>
		<?php do_action('comment_form', $post->ID); ?>
	</form>

<?php } // end registration check ?>
</div>
