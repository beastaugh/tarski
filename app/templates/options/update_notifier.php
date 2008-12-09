<div class="postbox">
	<h3><?php _e('Tarski Updates', 'tarski'); ?></h3>
	
	<div class="inside">
		<?php echo tarski_update_notifier(); ?>
		
		<?php if (!is_null(WP_Http::_getTransport())) { ?>
			<label for="update-on"><input type="radio" id="update-on" name="update_notification" value ="on" <?php if(get_tarski_option('update_notification')) { echo 'checked="checked" '; } ?>/> <?php _e('Update notification on (recommended)','tarski'); ?></label>
			<label for="update-off"><input type="radio" id="update-off" name="update_notification" value ="off" <?php if(!get_tarski_option('update_notification')) { echo 'checked="checked" '; } ?>/> <?php _e('Update notification off','tarski'); ?></label>
			
			<?php if (!cache_is_writable('version.atom') && get_tarski_option('update_notification')) { ?>
				<p><?php printf( __('The version check could not be cached. To enable caching, follow the tutorial on the %s page.','tarski'), '<a href="http://tarskitheme.com/help/updates/notifier/">' . __('update notifier','tarski') . '</a>' ); ?></p>
			<?php } ?>
		<?php } else { ?>
			<h4><?php _e('Update Notification','tarski'); ?></h4>
			<p><?php _e('Your server appears to lack the ability to access external websites. This means that the update notifier will not work.','tarski'); ?></p>
			<p><?php printf(
				__('You can read our %1$s on how to fix your server setup, but if you are unable to change it we recommend subscribing to either the %2$s or the %3$s in your feed reader, so that you can be alerted when new Tarski versions become available.','tarski'),
				'<a href="http://tarskitheme.com/help/updates/notifier/">' . __('documentation','tarski') . '</a>',
				'<a href="' . TARSKIVERSIONFILE . '">' . __('Tarski version feed','tarski') . '</a>',
				'<a href="http://tarskitheme.com/feed/">' . __('Tarski website feed','tarski') . '</a>'
			); ?></p>
		<?php } ?>
	</div>
</div>
