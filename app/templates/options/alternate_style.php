<?php
$style_dir = dir(TEMPLATEPATH . '/styles');
$styles = array();

if ($style_dir)
	while(($file = $style_dir->read()))
		if(is_valid_tarski_style($file))
			$styles[] = $file;

if (count($styles) > 0) { ?>
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

<?php if (detectWPMU()) { // WPMU users ?>
	<p><?php _e('Tarski allows you to select an alternate style that modifies the default one. Choose from the list above.','tarski'); ?></p>
<?php } else { // non-WPMU users ?>
	<p><?php printf( __('Tarski allows you to select an %1$s that modifies the default one. Choose from the list above, or upload your own to %2$s.', 'tarski'), '<a href="http://tarskitheme.com/help/styles/">' . __('alternate style', 'tarski') . '</a>', '<kbd>wp-content/themes/' . get_template() . '/styles/</kbd>' ); ?></p>
<?php } ?>

<?php if (count($styles) < 1)  { ?>
	<p><strong><?php _e('No alternate stylesheets appear to be present.', 'tarski'); ?></strong></p>
	<?php tarski_options_fragment('missing_files'); ?>
<?php } ?>
