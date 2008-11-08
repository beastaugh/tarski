<?php if (!get_tarski_option('deleted')) { if (isset($_GET['restored'])) { ?>
	<div id="updated" class="updated fade"><p><?php printf(
		__('Tarski options have been restored. %s', 'tarski'),
		'<a href="' . user_trailingslashit(get_bloginfo('url')) . '">' . __('View site &rsaquo;','tarski') . '</a>'
	); ?></p></div>
<?php } elseif (isset($_GET['updated'])) { ?>
	<div id="updated" class="updated fade"><p><?php printf(
		__('Tarski options have been updated. %s', 'tarski'),
		'<a href="' . user_trailingslashit(get_bloginfo('url')) . '">' . __('View site &rsaquo;','tarski') . '</a>'
	); ?></p></div>
<?php } } ?>

<?php if (get_tarski_option('deleted')) { ?>
	<div class="updated fade">
		<form action="<?php echo admin_url('admin-post.php?action=restore_tarski_options'); ?>" method="post">
			<?php wp_nonce_field('admin_post_restore_tarski_options', '_wpnonce_restore_tarski_options'); ?>
			<input type="hidden" name="restore_options" value="1" />
			<p><?php _e('You have deleted your Tarski options.','tarski'); ?> <input class="button" type="submit" name="submit" value="<?php _e('Restore Tarski Options &raquo;','tarski'); ?>" /></p>
		</form>
	</div>
<?php } ?>
