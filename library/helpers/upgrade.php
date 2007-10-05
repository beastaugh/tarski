<?php

if(get_option('tarski_options')) {
	if(get_tarski_option('installed') < theme_version()) {
		
		// 1.8 options update
		if(!get_tarski_option('hide_categories') || get_tarski_option('hide_categories') == '0') {
			add_tarski_option('show_categories', true);
		}
		drop_tarski_option('hide_categories');
		
		// 1.7 options update
		if(get_tarski_option('display_title') == "lolno") {
			update_tarski_option('display_title', false);
		}
		
		update_tarski_option('installed', theme_version());
	}
}

?>