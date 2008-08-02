<?php

// Localisation
load_theme_textdomain('tarski');

// Custom header image
define('HEADER_TEXTCOLOR', '');
define('HEADER_IMAGE', '%s/headers/' . get_tarski_option('header')); // %s is theme directory URI
define('HEADER_IMAGE_WIDTH', 720);
define('HEADER_IMAGE_HEIGHT', 180);
define('NO_HEADER_TEXT', true);
add_custom_image_header('', 'tarski_admin_header_style');

// Widgets
register_sidebar( // Main sidebar widgets
	array(
		'id' => 'sidebar-main',
		'name' => __('Main sidebar','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	)
);
register_sidebar( // Post and page sidebar widgets
	array(
		'id' => 'sidebar-post-and-page',
		'name' => __('Post and page sidebar','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	)
);
register_sidebar( // Footer main widgets
	array(
		'id' => 'footer-main',
		'name' => __('Footer main widgets','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	)
);
register_sidebar( // Footer sidebar widgets
	array(
		'id' => 'footer-sidebar',
		'name' => __('Footer sidebar widgets','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	)
);

// Tarski widgets
register_sidebar_widget(__('Recent Articles','tarski'), 'tarski_recent_entries');

// Widget filters
add_filter('widget_text', 'tarski_content_massage');
add_filter('widget_text', 'tarski_widget_text_wrapper');
add_filter('widget_links_args', 'tarski_widget_links_args');

if (is_admin()) {
	// Generate messages
	add_filter('tarski_messages', 'tarski_update_notifier');

	// Output messages on dashboard and options page
	add_action('admin_notices', 'tarski_messages');

	// Tarski Options page
	add_action('admin_print_styles', 'tarski_admin_style');
	add_action('admin_print_scripts-design_page_tarski-options', 'tarski_inject_scripts');
	add_action('admin_print_styles-design_page_tarski-options', 'tarski_inject_styles');
	add_action('admin_menu', 'tarski_addmenu');

	// Options
	add_action('save_post', 'tarski_resave_show_authors');
	add_action('deleted_post', 'tarski_resave_show_authors');
	add_action('save_post', 'flush_tarski_recent_entries');
	add_action('deleted_post', 'flush_tarski_recent_entries');
	add_action('switch_theme', 'flush_tarski_recent_entries');
	add_action('switch_theme', 'tarski_upgrade_and_flush_options');
}

// Header
add_action('wp_head', array('Asset', 'init'));

add_action('th_header', 'tarski_headerimage');
add_action('th_header', 'tarski_titleandtag');
add_action('th_header', 'navbar_wrapper');
add_action('th_header', 'tarski_next_prev_posts');

add_filter('tarski_navbar', 'add_external_links');
add_filter('tarski_navbar', 'add_admin_link', 20);
add_filter('tarski_navbar', 'wrap_navlist', 21);

add_action('th_navbar', 'tarski_navbar');
add_action('th_navbar', 'tarski_feedlink');

// Posts
add_action('parse_query', 'only_paginate_home');

add_action('th_postend', 'add_post_tags', 10);
add_action('th_postend', 'tarski_link_pages', 11);

add_action('th_posts_nav', 'tarski_posts_nav_link');

// Sidebar
add_filter('tarski_sidebar_custom', 'tarski_content_massage', 9);
add_filter('tarski_sidebar', 'hide_sidebar_for_archives');

add_action('th_sidebar', 'tarski_sidebar', 10);

// Comments
add_filter('avatar_defaults', 'tarski_default_avatar');
add_filter('get_comment_author', 'tidy_openid_names');
add_filter('get_avatar', 'tidy_avatars', 10, 4);

// Footer
add_action('th_fsidebar', 'tarski_footer_sidebar');
add_action('th_fmain', 'tarski_footer_main');
add_action('th_footer', 'tarski_feedlink');
add_action('th_footer', 'tarski_credits');

// Constants output
if(file_exists(TEMPLATEPATH . '/constants.php')) {
	include_once(TEMPLATEPATH . '/constants.php');
	
	add_filter('tarski_navbar', 'tarski_output_navbarinclude');
	add_filter('th_404_content', 'tarski_output_errorinclude');

	add_action('wp_head', 'tarski_output_headinclude');
	add_action('th_postend', 'tarski_output_frontpageinclude');
	add_action('th_postend', 'tarski_output_postendinclude', 12);
	add_action('th_postend', 'tarski_output_pageendinclude', 12);
	add_action('comment_form', 'tarski_output_commentsforminclude', 11);
	add_action('th_sidebar', 'tarski_output_sidebartopinclude', 9);
	add_action('th_sidebar', 'tarski_output_sidebarbottominclude', 11);
	add_action('th_sidebar', 'tarski_output_nosidebarinclude', 11);
	add_action('th_sidebar', 'tarski_output_archivesinclude', 9);
	add_action('th_fsidebar', 'tarski_output_searchtopinclude', 9);
	add_action('th_fsidebar', 'tarski_output_searchbottominclude', 11);
	add_action('th_footer', 'tarski_output_footerinclude');
}

?>