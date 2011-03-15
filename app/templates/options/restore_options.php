<?php if (is_numeric(get_raw_tarski_option('deleted'))) { ?>
    <div class="updated fade below-h2">
        <form action="<?php echo admin_url('admin-post.php?action=restore_tarski_options'); ?>" method="post">
            <?php wp_nonce_field('admin_post_restore_tarski_options', '_wpnonce_restore_tarski_options'); ?>
            <input type="hidden" name="restore_options" value="1" />
            <p><?php _e('You have deleted your Tarski options.','tarski'); ?> <input class="button" type="submit" name="submit" value="<?php _e('Restore Tarski Options &raquo;','tarski'); ?>" /></p>
        </form>
    </div>
<?php } ?>
