<div class="primary content">
	<p><?php _e('Powered by <a href="http://wordpress.org/">WordPress</a> and <a href="http://tarskitheme.com/">Tarski</a>', 'tarski');
	if(detectWPMU()) {
		echo ' | '. sprintf( __('Hosted by %s','tarski'), '<a href="http://'. $current_site->domain. $current_site->path. '">'. $current_site->site_name. '</a>' );
	} ?></p>
</div>
<div class="secondary">
	<p><a class="feed" href="<?php bloginfo($feed_url); ?>"><?php _e('Subscribe to feed','tarski'); ?></a></p>
</div>