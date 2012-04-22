<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Tarski's constants.
 *
 * These mostly provide convenient aliases for filesystem paths. Tarski's files
 * live in a number of directories (the main ones being /app and /library), so
 * keeping includes simple is greatly helped by a sane set of path constants.
 */
if (!defined('TARSKI_DEBUG'))
    define('TARSKI_DEBUG', false);
define('TARSKICLASSES', get_template_directory() . '/library/classes');
define('TARSKIHELPERS', get_template_directory() . '/library/helpers');
define('TARSKIDISPLAY', get_template_directory() . '/app/templates');

/**
 * Core library files.
 *
 * These files will be loaded whenever WordPress is. They include a few key
 * functions, and the core classes that Tarski requires to load its options,
 * add dependencies to document heads, and output comments.
 *
 * @see Options
 * @see TarskiCommentWalker
 */
require(get_template_directory() . '/library/core.php');
require(TARSKICLASSES . '/options.php');
require(TARSKICLASSES . '/comment_walker.php');

/**
 * Admin library files.
 *
 * These library files are required for Tarski's administrative functions. They
 * are loaded only when a WordPress admin page is accessed, so as to reduce the
 * load on the server.
 */
if (is_admin()) require(TARSKIHELPERS . '/admin_helper.php');

/**
 * Templating libraries.
 *
 * As a theme, particularly given its complexity and multiplicity of options,
 * Tarski needs a lot of templating functions. There is an ongoing effort to
 * split functions up into logical groups spread across more and smaller files,
 * so that each grouping remains comprehensible and each function easy to find.
 */
require(TARSKIHELPERS . '/template_helper.php');
require(TARSKIHELPERS . '/content_helper.php');
require(TARSKIHELPERS . '/comments_helper.php');
require(TARSKIHELPERS . '/author_helper.php');
require(TARSKIHELPERS . '/tag_helper.php');
require(TARSKIHELPERS . '/widgets.php');

/**
 * API files.
 *
 * Tarski's API is actually spread across much of the library files required
 * above, but certain pieces of functionality such as generic template hooks,
 * legacy API handlers, and deprecated functions, all live in specialised API
 * files where they can be easily found and documented.
 */
require(get_template_directory() . '/app/api/hooks.php');
include(get_template_directory() . '/app/api/deprecated.php');

/**
 * Launcher.
 *
 * The following code makes an inital round of function calls, loading any
 * available localisation files, defining several constants which WordPress
 * requires, registering widget sidebars, and adding numerous actions and
 * filters.
 */

// Localisation
load_theme_textdomain('tarski', get_template_directory() . '/languages');

// Custom header support
add_theme_support('custom-header', array(
    'default-image'          => '%s/headers/' . get_tarski_option('header'),
    'random-default'         => false,
    'width'                  => 720,
    'height'                 => 180,
    'flex-height'            => false,
    'flex-width'             => false,
    'default-text-color'     => '',
    'header-text'            => false,
    'uploads'                => true,
    'wp-head-callback'       => '',
    'admin-head-callback'    => 'tarski_admin_header_style',
    'admin-preview-callback' => ''
));

register_default_headers(_tarski_list_header_images());

// Content width; set this in a plugin or child theme if you want to change
// the width of the theme via a stylesheet.
if (!isset($content_width)) {
    $content_width = 500;
}

// Main sidebar widgets
register_sidebar(array(
    'id'            => 'sidebar-main',
    'name'          => __('Main sidebar','tarski'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'));

// Post and page sidebar widgets
register_sidebar(array(
    'id'            => 'sidebar-post-and-page',
    'name'          => __('Post and page sidebar','tarski'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'));

// Footer main widgets
register_sidebar(array(
    'id'            => 'footer-main',
    'name'          => __('Footer main widgets','tarski'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'));

// Footer sidebar widgets
register_sidebar( array(
    'id'            => 'footer-sidebar',
    'name'          => __('Footer sidebar widgets','tarski'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'));

// Tarski widgets
register_widget('Tarski_Widget_Recent_Entries');

// Widget filters
add_filter('widget_text', 'tarski_content_massage');
add_filter('widget_text', 'tarski_widget_text_wrapper');
add_filter('widget_links_args', 'tarski_widget_links_args');

// Automatic feed links
add_theme_support('automatic-feed-links');

// Register navbar location
register_nav_menu('tarski_navbar', __('Tarski navbar', 'tarski'));

// Custom background support
add_theme_support('custom-background');

// Post thumbnails; change these settings via a child theme or plugin
add_theme_support('post-thumbnails');

if (get_tarski_option('featured_header')) {
    set_post_thumbnail_size(HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true);
} else {
    set_post_thumbnail_size($content_width, 300, false);
}

// Image size for large feature images, used in the header
add_image_size('large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true);

// Image size for featured posts if a large-feature doesn't exist
add_image_size('small-feature', $content_width, 300);

// Post types
add_theme_support('post-formats', array('aside'));

// Editor style
add_editor_style('library/css/editor.css');

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
} else {
    // JavaScript
    add_action('wp_enqueue_scripts', 'tarski_enqueue_scripts');
}

// Header
add_action('wp_head', 'tarski_meta', 9);
add_action('wp_head', 'tarski_stylesheets', 9);
add_filter('gallery_style', 'trim_gallery_style', 20);
add_filter('wp_title', 'tarski_document_title', 10, 3);

add_action('th_header', 'tarski_headerimage');
add_action('th_header', 'tarski_titleandtag');
add_action('th_header', 'navbar_wrapper');
add_action('th_header', 'tarski_next_prev_posts');

add_filter('body_class', 'tarski_body_class');

add_action('th_navbar', 'tarski_navbar');
add_action('th_navbar', 'tarski_feedlink');

// Posts
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