<?php

$styles = _tarski_list_alternate_styles();

if (count($styles) > 0) { ?>
    <select name="alternate_style" id="alternate_style" size="1">
        <option<?php if(!get_tarski_option('style')) { echo ' selected="selected"'; } ?> value=""><?php _e('Default style','tarski'); ?></option>
        <?php foreach($styles as $style) {
            printf(
                '<option%1$s value="%2$s">%3$s</option>' . "\n",
                $style['current'] ? ' selected="selected"' : '',
                $style['name'],
                $style['public']);
        } ?>
    </select>
<?php } ?>

<?php if (is_multisite()) { // WP Multisite users ?>
    <p><?php _e('Tarski allows you to select an alternate style that modifies the default one. Choose from the list above.','tarski'); ?></p>
<?php } else { // non-WP Multisite users ?>
    <p><?php printf( __('Tarski allows you to select an %1$s that modifies the default one. Choose from the list above, or upload your own to %2$s.', 'tarski'), '<a href="http://tarskitheme.com/help/styles/">' . __('alternate style', 'tarski') . '</a>', '<kbd>wp-content/themes/' . get_template() . '/styles/</kbd>' ); ?></p>
<?php } ?>

<?php if (count($styles) < 1)  { ?>
    <p><strong><?php _e('No alternate stylesheets appear to be present.', 'tarski'); ?></strong></p>
    <?php tarski_options_fragment('missing_files'); ?>
<?php } ?>
