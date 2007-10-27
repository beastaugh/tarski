<?php

// Custom header image
add_custom_image_header('', 'tarski_admin_header_style');

// Widgets
register_sidebar( // Main sidebar
	array(
		'name' => __('Main Sidebar','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	)
);
register_sidebar( // Post and page sidebar
	array(
		'name' => __('Post and Page Sidebar','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	)
);
register_sidebar( // Footer sidebar
	array(
		'name' => __('Footer Widgets','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	)
);

// Dashboard Tarski update notifier
add_action('activity_box_end', 'tarski_update_notifier');

// Tarski Options page
add_action('admin_menu', 'tarski_addmenu');
add_action('admin_head', 'tarski_inject_scripts');
add_action('save_post', 'tarski_resave_navbar');

// Header
add_action('wp_head', 'add_robots_meta');
add_action('wp_head', 'tarski_feeds');

add_action('th_header', 'tarski_headerimage');
add_action('th_header', 'tarski_titleandtag');

add_filter('tarski_navbar', 'add_external_links');
add_filter('tarski_navbar', 'add_admin_link', 20);
add_filter('tarski_navbar', 'wrap_navlist', 21);

add_action('th_navbar', 'tarski_navbar');
add_action('th_navbar', 'tarski_feedlink');

// Posts
add_action('th_postend', 'add_post_tags', 10);
add_action('th_postend', 'tarski_link_pages', 11);

// Sidebar
add_action('th_sidebar', 'tarski_sidebar', 10);

// Comments
add_filter('get_comment_author', 'tidy_openid_names');

// Footer
add_filter('tarski_footer_blurb', 'tarski_blurb_wrapper');

add_action('th_fsidebar', 'tarski_searchform');
add_action('th_footer', 'tarski_feedlink');
add_action('th_footer', 'tarski_credits');

// Constants output
if(file_exists(TEMPLATEPATH . '/constants.php')) {	
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