<h3><?php _e('Tarski Updates','tarski'); ?></h3>

<?php if(get_tarski_option('update_notification')) { ?>		
	<?php if($status == "current") { ?>
		
		<p><?php _e('Your version of Tarski is up to date.','tarski'); ?></p>
		
	<?php } elseif($status == "not_current") { ?>
		
		<div class="updated">
			<p><?php echo __('A new version of the Tarski theme, version ','tarski'). '<strong>'. $latest. '</strong>'. __(', ','tarski'). '<a href="'. $latest_link. '">'. __('is now available','tarski'). '</a>'. __('. Your installed version is ','tarski'). '<strong>'. $current. '</strong>'. __('.','tarski'); ?></p>
		</div>
		
	<?php } elseif($status == "no_connection") { ?>

		<p><?php echo __('No connection to update server. Your installed version is ','tarski'). '<strong>'. $current. '</strong>'. __('.','tarski'); ?></p>
		
	<?php } ?>
<?php } else { ?>
	<p><?php echo __('Update notification for Tarski is disabled. You can enable it on the ','tarski'). '<a href="'. get_bloginfo('wpurl'). '/wp-admin/themes.php?page=tarski-options">'. __('Tarski Options page','tarski'). '</a>'. __('.','tarski'); ?></p>
<?php } ?>