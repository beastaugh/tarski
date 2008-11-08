<div id="tarski-options" class="wrap tarski-options<?php if(get_bloginfo("text_direction") == "rtl") { echo " rtl"; } ?>">
	<div class="metabox-holder">
		
	<?php tarski_options_fragment('messages'); ?>
	
	<form action="<?php echo admin_url('admin-post.php?action=tarski_options'); ?>" method="post">
	
		<?php wp_nonce_field('admin_post_tarski_options', '_wpnonce_tarski_options'); ?>
	
		<div id="tarski-options-header">
			<h2><?php _e('Tarski Options', 'tarski'); ?></h2>
		
			<p id="tarski-save-options">
				<input type="submit" class="button-primary" name="submit" value="<?php _e('Save Options','tarski'); ?>" />
			</p>
		
			<p id="tarski-info">
				<a href="http://tarskitheme.com/help/"><?php _e('Tarski documentation','tarski'); ?></a>
				| <a href="http://tarskitheme.com/credits/"><?php _e('Credits &amp; Thanks','tarski'); ?></a>
				| <a href="http://tarskitheme.com/forum/"><?php _e('Forum','tarski'); ?></a>
			</p>
		
			<div class="clearer"></div>
		</div>
		
		<div id="tarski-update-notifier" class="secondary">
			<?php tarski_options_fragment('update_notifier'); ?>
		</div>
	
		<div class="primary">
			<?php tarski_options_fn_block('tarski_navbar_select', __('Navigation Display', 'tarski')); ?>
			<?php tarski_options_block('nav_options', __('Navigation Options', 'tarski')); ?>
		</div>
	
		<div class="secondary">
			<?php tarski_options_block('alternate_style', __('Alternate Style', 'tarski')); ?>
			<?php tarski_options_block('asides_category', __('Asides Category', 'tarski')); ?>
			<?php tarski_options_block('sidebar_options', __('Sidebar Options', 'tarski')); ?>
		</div>
	
		<div class="span">
			<?php tarski_options_block('header_images', __('Header Images', 'tarski')); ?>
		</div>
	
		<div class="primary">
			<?php tarski_options_fn_block('tarski_miscellaneous_options', __('Miscellaneous Options', 'tarski')); ?>
		</div>
	</form>
	
	<?php if (get_option('tarski_options') && !get_tarski_option('deleted')) { ?>
		<div class="secondary">
			<?php tarski_options_block('reset_options', __('Reset Options', 'tarski')); ?>
		</div>
	<?php } ?>
	
		<div class="clearer"></div>
	</div>
</div>
