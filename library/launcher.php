<?php

// Admin
if(function_exists('add_custom_image_header')) {
	tarski_config_custom_header();
	add_custom_image_header('', 'tarski_admin_header_style');
}

if(function_exists('register_sidebar')) {
	register_sidebar(array(
		'name' => __('Main Sidebar','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => __('Footer Widgets','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
}

if(!detectWPMU() || detectWPMUadmin()) {
	add_action('activity_box_end', 'tarski_update_notifier');
}

add_action('admin_menu', 'tarski_addmenu');
add_action('admin_head', 'tarski_inject_scripts');
add_action("save_post", "tarski_resave_navbar");


// Header
add_action('wp_head', 'add_robots_meta');
add_action('wp_head', 'tarski_feeds');

add_action('th_header', 'tarski_headerimage');
add_action('th_header', 'tarski_titleandtag');

add_filter('tarski_navbar', 'add_external_links');
add_filter('tarski_navbar', 'add_admin_link', 20);
add_filter('tarski_navbar', 'wrap_navlist', 21);

add_action('th_navbar', 'tarski_navbar');
add_action('th_navbar', 'tarski_navbar_feedlink');


// Content
add_action('th_postend', 'add_post_tags');
add_action('th_postend', 'link_pages_without_spaces');

add_filter('get_comment_author', 'tidy_openid_names');


// Sidebar


// Footer
add_filter('tarski_footer_blurb', 'tarski_blurb_wrapper');

add_action('th_fsidebar', 'tarski_searchform');
add_action('th_footer', 'tarski_feed_and_credit');


// Constants
if(file_exists(TEMPLATEPATH . '/constants.php')) {	
	add_filter('tarski_navbar', 'tarski_output_navbarinclude');
	add_filter('th_404_content', 'tarski_output_errorinclude');
	
	add_action('wp_head', 'tarski_output_headinclude');
	add_action('th_postend', 'tarski_output_frontpageinclude');
	add_action('th_postend', 'tarski_output_postendinclude');
	add_action('th_postend', 'tarski_output_pageendinclude');
	add_action('comment_form', 'tarski_output_commentsforminclude',11);
	add_action('th_sidebar', 'tarski_output_sidebartopinclude');
	add_action('th_sidebar', 'tarski_output_nosidebarinclude');
	add_action('th_sidebar', 'tarski_output_archivesinclude');
	add_action('th_fsidebar', 'tarski_output_searchtopinclude',9);
	add_action('th_fsidebar', 'tarski_output_searchbottominclude',11);
	add_action('th_footer', 'tarski_output_footerinclude');
}

?>