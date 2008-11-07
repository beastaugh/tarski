<div id="tarski-headers">
	<?php
	$name = get_tarski_option('header');
	if ( $header_dir = dir(TEMPLATEPATH . '/headers' ) ) {
		while(($file = $header_dir->read()) !== false) {
			if(!preg_match('|^\.+$|', $file) && preg_match('@\-thumb.(jpg|png|gif)$@', $file)) {
				$header_images[] = $file;
			}
		}
		if ($header_dir || $header_images) {
			$count = 0;
			foreach($header_images as $header_image) {
				$count++;
				$header_name = str_replace('-thumb', '', $header_image); ?>
				<label id="for_header_<?php echo $header_name; ?>" for="header_<?php echo $header_name; ?>"><img class="header_image" alt="<?php echo $header_name; ?>" src="<?php echo get_bloginfo('template_directory') . '/headers/' . $header_image; ?>" /></label>
				<input id="header_<?php echo $header_name; ?>" name="header_image" value="<?php echo $header_name; ?>" type="radio"<?php if(get_tarski_option('header') == $header_name) { echo ' checked="checked"'; } ?> />
			<?php }
		}
	} ?>
		<div class="clearer"></div>
	</div>
	
	<p><?php printf( __('Choose a header image by clicking on it. The current image is the %s one.','tarski'), '<span class="highlight">' . __('highlighted','tarski') . '</span>' ); ?></p>
<?php if(!detectWPMU()) { ?>
<div class="tip">
	<p><?php printf( __('You can upload your own header images (.gif, .jpg or .png) to %s.','tarski'), '<code>wp-content/themes/' . get_template() . '/headers/</code>' ); ?></p>
	<p><?php printf( __('Make sure that you upload a thumbnail file as well. If your image is named %1$s, the corresponding thumbnail file should be named %2$s.','tarski'), '<code>'. __('example','tarski'). '.jpg</code>', '<code>'. __('example','tarski'). '-thumb.jpg</code>'); ?></p>
</div>
<?php } ?>
