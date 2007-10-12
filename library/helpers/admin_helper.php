<?php

/**
 * Detects whether WordPress Multi-User is in use.
 */
function detectWPMU() {
	return function_exists('is_site_admin');
}

/**
 * If WordPress Multi-User is in use, detect whether
 * the current user is the site administrator.
 */
function detectWPMUadmin() {
	if(detectWPMU()) {
		return is_site_admin();
	}
}

/**
 * Checks whether a given file name is permitted as a
 * Tarski alternate style name.
 * 
 * It must be a valid CSS identifier, followed by the
 * .css file extension, and it cannot have a name that
 * is already taken by Tarski's CSS namepsace.
 */
function is_tarski_style($file) {
	return (bool) (
		!preg_match('/^\.+$/', $file)
		&& preg_match('/^[A-Za-z][A-Za-z0-9\-]*.css$/', $file)
		&& !preg_match('/^janus.css$|^single.css$|^centre.css$|^left.css$|^rtl.css$/', $file)
	);
}

/**
 * Sets the parameters for using WordPress's custom
 * header image functionality with Tarski.
 */
function tarski_config_custom_header() {
	define('HEADER_TEXTCOLOR', '');
	define('HEADER_IMAGE', '%s/headers/' . get_tarski_option('header'));
	// %s is theme dir uri
	define('HEADER_IMAGE_WIDTH', 720);
	define('HEADER_IMAGE_HEIGHT', 180);
	define('NO_HEADER_TEXT', true );
}

/**
 * Styles the custom header image admin page for use
 * with Tarski.
 */
function tarski_admin_header_style() {
	include(TARSKIDISPLAY."/admin/admin_header_style.php");
}

/**
 * Adds JavaScript and CSS to the Tarski Options page.
*/
function tarski_inject_scripts() {
	if(substr($_SERVER['REQUEST_URI'], -39, 39) == 'wp-admin/themes.php?page=tarski-options') { // Hack detects Tarski Options page
		include(TARSKIDISPLAY . "/admin/options_scripts.php");
	}
}

/**
 * Adds the Tarski Options page to the WordPress admin panel.
 */
function tarski_addmenu() {
	add_submenu_page('themes.php', __('Tarski Options','tarski'), __('Tarski Options','tarski'), 'edit_themes', 'tarski-options', 'tarski_admin');
}

/**
 * Saves Tarski's options, and displays the Options page.
 */
function tarski_admin() {
	save_tarski_options();
	include(TARSKIDISPLAY . "/admin/options_page.php");
}

/**
 * Re-saves Tarski's navbar order whenever a page is edited.
 * 
 * This means that if the page order changes, the navbar order
 * will change too.
 */
function tarski_resave_navbar() { // Changing page order changes navbar order
	global $wpdb;
	$pages = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page' ORDER BY post_parent, menu_order");
	$selected = explode(',', get_tarski_option("nav_pages"));

	if($pages) {
		$nav_pages = array();
		foreach ($pages as $key => $page) {
			foreach($selected as $key2 => $sel_page) {
				if ($page->ID == $sel_page) {
					$nav_pages[$key] = $page->ID;
				}
			}
		}
		$condensed = implode(",", $nav_pages);
	}
	
	update_tarski_option("nav_pages", $condensed);
}

?>