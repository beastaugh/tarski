<div class="content">
	<h3><?php _e('Error', 'tarski'); ?></h3>
	<p><?php printf( __('%s not found.', 'tarski'), '<code>user-sidebar.php</code>' ); ?></p>
	<p><?php printf( __('Either upload a %1$s to your Tarski directory, or select another sidebar type from the %2$s.', 'tarski'), '<a href="http://tarskitheme.com/help/sidebar/custom/">' . __('custom sidebar file', 'tarski') . '</a>', '<a href="' . get_bloginfo('wpurl') . '/wp-admin/themes.php?page=tarski-options">' . __('Tarski Options page', 'tarski') . '</a>' ); ?></p>
	<p><?php _e('Normal users will see the default Tarski sidebar until this is resolved.'); ?></p>
</div>