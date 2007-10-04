<?php

// upgrade to serialised options, implemented in 1.4...
if(!get_option('tarski_options')) {
	$tarski_options = array();
	
	$tarski_options['sidebar_type'] = get_option('tarski_sidebar_type');
	$tarski_options['update_notification'] = get_option('tarski_update_notification');
	$tarski_options['header'] = get_option('tarski_header');
	$tarski_options['nav_pages'] = get_option('tarski_nav_pages');
	$tarski_options['centered_theme'] = get_option('tarski_centered_theme');
	$tarski_options['swap_sides'] = get_option('tarski_swap_sides');
	$tarski_options['style'] = get_option('tarski_style');
	$tarski_options['installed'] = get_option('tarski_installed');
	$tarski_options['blurb'] = get_option('blurb');
	$tarski_options['asidescategory'] = get_option('tarski_asidescategory');
	$tarski_options['about_text'] = get_option('about_text');
	$tarski_options['footer_recent'] = get_option('tarski_footer_recent');
	$tarski_options['sidebar_pages'] = get_option('tarski_sidebar_pages');
	$tarski_options['sidebar_links'] = get_option('tarski_sidebar_links');
	$tarski_options['sidebar_custom'] = get_option('tarski_sidebar_custom');
	$tarski_options['sidebar_onlyhome'] = get_option('tarski_sidebar_onlyhome');
	$tarski_options['display_title'] = get_option('tarski_display_title');
	$tarski_options['display_tagline'] = get_option('tarski_display_tagline');
	$tarski_options['hide_categories'] = get_option('tarski_hide_categories');
	$tarski_options['use_pages'] = get_option('tarski_use_pages');
	
	ksort($tarski_options);
	$tarski_options = serialize($tarski_options);
	update_option('tarski_options', $tarski_options);
	
	delete_option('tarski_sidebar_type');
	delete_option('tarski_update_notification');
	delete_option('tarski_header');
	delete_option('tarski_nav_pages');
	delete_option('tarski_centered_theme');
	delete_option('tarski_swap_sides');
	delete_option('tarski_style');
	delete_option('tarski_installed');
	delete_option('blurb');
	delete_option('tarski_asidescategory');
	delete_option('about_text');
	delete_option('tarski_footer_recent');
	delete_option('tarski_sidebar_pages');
	delete_option('tarski_sidebar_links');
	delete_option('tarski_sidebar_comments');
	delete_option('tarski_sidebar_custom');
	delete_option('tarski_sidebar_onlyhome');
	delete_option('tarski_display_title');
	delete_option('tarski_display_tagline');
	delete_option('tarski_hide_categories');
	delete_option('tarski_use_pages');
	delete_option('tarski_ajax_tags');
	
	// hopefully bypass errors?
	echo "<script>window.location.replace('${_SERVER['REQUEST_URI']}');</script>\n";
	exit;
}

// 1.7 options update
if(get_tarski_option('display_title') == "lolno") {
	update_tarski_option('display_title', false);
}

// 1.8 options update
if(get_tarski_option('update_notification') == 'true') {
	update_tarski_option('update_tarski_option', true);
} elseif(get_tarski_option('update_notification') == 'false') {
	update_tarski_option('update_tarski_option', false);
}

?>