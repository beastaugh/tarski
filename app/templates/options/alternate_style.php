<?php
$style_dir = dir(TEMPLATEPATH . '/styles');
if($style_dir) {
	while(($file = $style_dir->read()) !== false) {
		if(is_valid_tarski_style($file)) {
			$styles[] = $file;
		}
	}
}
if($style_dir && $styles) { ?>
	<select name="alternate_style" id="alternate_style" size="1">
		<option<?php if(!get_tarski_option('style')) { echo ' selected="selected"'; } ?> value=""><?php _e('Default style','tarski'); ?></option>
		<?php foreach($styles as $style) {
			if(get_tarski_option('style') == $style) {
				$status = ' selected="selected"';
			} else {
				$status = false;
			}
			printf(
				'<option%1$s value="%2$s">%3$s</option>'."\n",
				$status,
				$style,
				$style
			);
		} ?>
	</select>
<?php } ?>

<?php if(detectWPMU()) { // WPMU users ?>
	<p><?php _e('Tarski allows you to select an alternate style that modifies the default one. Choose from the list above.','tarski'); ?></p>
<?php } else { // non-WPMU users ?>
	<p><?php printf( __('Tarski allows you to select an %1$s that modifies the default one. Choose from the list above, or upload your own to %2$s.','tarski'), '<a href="http://tarskitheme.com/help/styles/">'. __('alternate style','tarski'). '</a>', '<code>wp-content/themes/' . get_template() . '/styles/</code>' ); ?></p>
<?php } ?>
