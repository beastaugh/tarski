<div id="tarski-headers">
	<?php
	$name = get_tarski_option('header');
	$header_images = array();
	
	if ($header_dir = dir(TEMPLATEPATH . '/headers')) {
		while (($file = $header_dir->read()))
			if (!preg_match('|^\.+$|', $file) && preg_match('@\-thumb.(jpg|png|gif)$@', $file))
				$header_images[] = $file;
		
		if (count($header_images) > 0) {
			$count = 0;
			foreach($header_images as $header_image) {
				$count++;
				$header_name = str_replace('-thumb', '', $header_image); ?>
				<label id="for_header_<?php echo $header_name; ?>" for="header_<?php echo $header_name; ?>"><img class="header_image" alt="<?php echo $header_name; ?>" src="<?php echo get_bloginfo('template_directory') . '/headers/' . $header_image; ?>" /></label>
				<input id="header_<?php echo $header_name; ?>" name="header_image" value="<?php echo $header_name; ?>" type="radio"<?php if(get_tarski_option('header') == $header_name) { echo ' checked="checked"'; } ?> />
			<?php }
		} else { ?>
			<p><strong><?php _e('No header images appear to be present.', 'tarski'); ?></strong></p>
			<p><?php _e('This may be because you have removed them, or because those files aren&#8217;t readable by WordPress.', 'tarski'); ?></p>
			<p><?php printf(__('If you have problems making the files readable, try the WordPress Codex documentation on %s.', 'tarski'), '<a href="http://codex.wordpress.org/Changing_File_Permissions">' . __('changing file permissions', 'tarski') . '</a>'); ?></p>
			<p><?php printf(__('If you deleted the files by accident, just download a new copy from the %s and re-upload them to your website.', 'tarski'), '<a href="http://tarskitheme.com/">' . __('Tarski website', 'tarski') . '</a>'); ?>
		<?php }
	} ?>
		<div class="clearer"></div>
	</div>
	
	<?php if (count($header_images) > 0) { ?>
	<p><?php printf( __('Choose a header image by clicking on it. The current image is the %s one.','tarski'), '<span class="highlight">' . __('highlighted','tarski') . '</span>' ); ?></p>
	<?php } ?>
<?php if(!detectWPMU()) { ?>
<div class="details">
	<p><?php printf( __('You can upload your own header images (.gif, .jpg or .png) to %s.','tarski'), '<kbd>wp-content/themes/' . get_template() . '/headers/</kbd>' ); ?></p>
	<p><?php printf( __('Make sure that you upload a thumbnail file as well. If your image is named %1$s, the corresponding thumbnail file should be named %2$s.','tarski'), '<kbd>'. __('example','tarski'). '.jpg</kbd>', '<kbd>'. __('example','tarski'). '-thumb.jpg</kbd>'); ?></p>
</div>
<?php } ?>
