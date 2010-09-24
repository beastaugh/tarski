<div id="tarski-headers">
    <?php
    $name    = get_tarski_option('header');
    $headers = _tarski_list_header_images();
    if (count($headers) > 0) { foreach ($headers as $header) { ?>
        <label id="<?php echo $header['lid']; ?>" for="<?php echo $header['id']; ?>"><img class="header_image" alt="<?php echo $header['name']; ?>" src="<?php echo $header['thumb'] ?>" /></label>
        <input id="<?php echo $header['id']; ?>" name="header_image" value="<?php echo $header['name']; ?>" type="radio"<?php if ($header['current']) echo ' checked="checked"'; ?> />
    <?php } } else { ?>
        <p><strong><?php _e('No header images appear to be present.', 'tarski'); ?></strong></p>
        <?php tarski_options_fragment('missing_files'); ?>
    <?php } ?>
    <div class="clearer"></div>
</div>

<?php if (count($headers) > 0) { ?>
    <p><?php printf( __('Choose a header image by clicking on it. The current image is the %s one.','tarski'), '<span class="highlight">' . __('highlighted','tarski') . '</span>' ); ?></p>
<?php } if (!is_multisite()) { ?>
<div class="details">
    <p><?php printf( __('You can upload your own header images (.gif, .jpg or .png) to %s.','tarski'), '<kbd>wp-content/themes/' . get_template() . '/headers/</kbd>' ); ?></p>
    <p><?php printf( __('Make sure that you upload a thumbnail file as well. If your image is named %1$s, the corresponding thumbnail file should be named %2$s.','tarski'), '<kbd>'. __('example','tarski'). '.jpg</kbd>', '<kbd>'. __('example','tarski'). '-thumb.jpg</kbd>'); ?></p>
</div>
<?php } ?>
