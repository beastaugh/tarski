<h3><?php _e('Tarski Updates','tarski'); ?></h3>

<?php if(get_tarski_option('update_notification')) { ?>		
	<?php if($status == "current") { ?>
		
		<p><?php _e('Your version of Tarski is up to date.','tarski'); ?></p>
		
	<?php } elseif($status == "not_current") { ?>
		
		<div class="updated">
			<p><?php printf( __('A new version of the Tarski theme, version %1$s %2$s. Your installed version is %3$s.','tarski'), '<strong>'. $latest. '</strong>', '<a href="'. $latest_link. '">'. __('is now available','tarski'). '</a>', '<strong>'. $current. '</strong>' ); ?></p>
		</div>
		
	<?php } elseif($status == "no_connection") { ?>

		<p><?php printf( __('No connection to update server. Your installed version is %s.','tarski'), '<strong>'. $current. '</strong>' ); ?></p>
		
	<?php } ?>
<?php } else { ?>
	<p><?php printf( __('Update notification for Tarski is disabled. You can enable it on the %s page.','tarski'), '<a href="'. get_bloginfo('wpurl'). '/wp-admin/themes.php?page=tarski-options">'. __('Tarski Options','tarski'). '</a>' ); ?></p>
<?php } ?>