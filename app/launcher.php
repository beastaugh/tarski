<?php

// Localisation
load_theme_textdomain('tarski');

// Custom header image
if (!defined('HEADER_TEXTCOLOR'))
	define('HEADER_TEXTCOLOR', '');
if (!defined('HEADER_IMAGE')) // %s is theme directory URI
	define('HEADER_IMAGE', '%s/headers/' . get_tarski_option('header'));
if (!defined('HEADER_IMAGE_WIDTH'))
	define('HEADER_IMAGE_WIDTH', 720);
if (!defined('HEADER_IMAGE_HEIGHT'))
	define('HEADER_IMAGE_HEIGHT', 180);
if (!defined('NO_HEADER_TEXT'))
	define('NO_HEADER_TEXT', true);
add_custom_image_header('', 'tarski_admin_header_style');

// Content width; set this in a plugin or child theme if you want to change
// the width of the theme via a stylesheet.
if (!isset($content_width))
    $content_width = 500;

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
register_widget('Tarski_Widget_Recent_Entries');

// Widget filters
add_filter('widget_text', 'tarski_content_massage');
add_filter('widget_text', 'tarski_widget_text_wrapper');
add_filter('widget_links_args', 'tarski_widget_links_args');

// Automatic feed links
add_theme_support('automatic-feed-links');

// Custom background support
add_custom_background();

// Post thumbnails; change these settings via a child theme or plugin
add_theme_support('post-thumbnails');
set_post_thumbnail_size(150, 150, false);

if (is_admin()) {
	// Options handlers
	add_action('admin_post_tarski_options', 'save_tarski_options');
	add_action('admin_post_delete_tarski_options', 'delete_tarski_options');
	add_action('admin_post_restore_tarski_options', 'restore_tarski_options');
	
	// Tarski Options page
	add_action('admin_menu', 'tarski_addmenu');

	// Options
	add_action('admin_head', 'tarski_upgrade_and_flush_options');
	add_action('admin_head', 'maybe_wipe_tarski_options');
	add_action('save_post', 'tarski_resave_show_authors');
	add_action('deleted_post', 'tarski_resave_show_authors');
}

// Header
add_action('wp_head', 'tarski_meta', 9);
add_action('wp_head', 'tarski_stylesheets', 9);
add_action('wp_head', 'tarski_javascript', 9);
add_filter('gallery_style', 'trim_gallery_style', 20);

add_action('th_header', 'tarski_headerimage');
add_action('th_header', 'tarski_titleandtag');
add_action('th_header', 'navbar_wrapper');
add_action('th_header', 'tarski_next_prev_posts');

add_filter('body_class', 'tarski_body_class');

add_filter('tarski_navbar', '_tarski_navbar_home_link');
add_filter('tarski_navbar', '_tarski_navbar_page_links');
add_filter('tarski_navbar', 'add_external_links');
add_filter('tarski_navbar', 'add_admin_link', 20);
add_filter('tarski_navbar', 'wrap_navlist', 21);

add_action('th_navbar', 'tarski_navbar');
add_action('th_navbar', 'tarski_feedlink');

// Posts
add_action('parse_query', 'only_paginate_home');

add_action('wp_head', 'tarski_post_metadata');

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

?>