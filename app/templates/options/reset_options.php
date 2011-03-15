<form action="<?php echo admin_url('admin-post.php?action=delete_tarski_options'); ?>" method="post">
    <p><input class="button-secondary" type="submit" name="submit" value="<?php _e('Reset Tarski&#8217;s options to their default values','tarski'); ?>" /></p>
    <?php wp_nonce_field('admin_post_delete_tarski_options', '_wpnonce_delete_tarski_options'); ?>
    <input type="hidden" name="delete_options" value="1" />
</form>

<p><?php _e('If you change your mind, you&#8217;ll have three hours to restore your options before they&#8217;re removed for good.','tarski'); ?></p>
