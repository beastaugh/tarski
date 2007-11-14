<?php

/**
 * detectWPMU() - Detects whether WordPress Multi-User is in use.
 * 
 * @since 1.4
 * @return boolean
 */
function detectWPMU() {
	return function_exists('is_site_admin');
}

/**
 * detectWPMUadmin() - Detect whether the current user is a WPMU site administrator.
 * 
 * @since 2.0
 * @return boolean
 */
function detectWPMUadmin() {
	if(detectWPMU())
		return is_site_admin();
}

/**
 * is_valid_tarski_style() - Checks whether a given file name is a valid Tarski stylesheet name.
 * 
 * It must be a valid CSS identifier, followed by the .css file extension,
 * and it cannot have a name that is already taken by Tarski's CSS namepsace.
 * @since 2.0
 * @param string $file
 * @return boolean
 */
function is_valid_tarski_style($file) {
	return (bool) (
		!preg_match('/^\.+$/', $file)
		&& preg_match('/^[A-Za-z][A-Za-z0-9\-]*.css$/', $file)
		&& !preg_match('/^janus.css$|^centre.css$|^rtl.css$/', $file)
	);
}

/**
 * tarski_admin_header_style() - Styles the custom header image admin page for use with Tarski.
 * 
 * @since 1.4
 */
function tarski_admin_header_style() {
	include(TARSKIDISPLAY."/admin/admin_header_style.php");
}

/**
 * tarski_inject_scripts() - Adds JavaScript and CSS to the Tarski Options page.
 * 
 * @since 1.4
*/
function tarski_inject_scripts() {
	if(substr($_SERVER['REQUEST_URI'], -39, 39) == 'wp-admin/themes.php?page=tarski-options')
		include(TARSKIDISPLAY . "/admin/options_scripts.php");
}

/**
 * tarski_addmenu() - Adds the Tarski Options page to the WordPress admin panel.
 * 
 * @since 1.0
 */
function tarski_addmenu() {
	add_submenu_page('themes.php', __('Tarski Options','tarski'), __('Tarski Options','tarski'), 'edit_themes', 'tarski-options', 'tarski_admin');
}

/**
 * tarski_admin() - Saves Tarski's options, and displays the Options page.
 * 
 * @since 1.0
 */
function tarski_admin() {
	save_tarski_options();
	$widgets_link = get_bloginfo('wpurl') . '/wp-admin/widgets.php';
	$tarski_options_link = get_bloginfo('wpurl') . '/wp-admin/themes.php?page=tarski-options';
	include(TARSKIDISPLAY . "/admin/options_page.php");
}

/**
 * tarski_resave_navbar() - Re-saves Tarski's navbar order whenever a page is edited.
 * 
 * This means that if the page order changes, the navbar order will change too.
 * @since 1.7
 */
function tarski_resave_navbar() {
	global $wpdb;
	$pages = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page' ORDER BY post_parent, menu_order");
	$selected = explode(',', get_tarski_option('nav_pages'));

	if($pages) {
		$nav_pages = array();
		foreach ($pages as $key => $page) {
			foreach($selected as $key2 => $sel_page) {
				if ($page->ID == $sel_page) {
					$nav_pages[$key] = $page->ID;
				}
			}
		}
		$condensed = implode(',', $nav_pages);
	}
	
	update_tarski_option('nav_pages', $condensed);
}

/**
 * tarski_count_authors() - Returns the number of authors on a site.
 * 
 * This function returns the number of users on a site with a user
 * level of greater than 1, i.e. Authors, Editors and Administrators.
 * @since 2.0.3
 * @global object $wpdb
 * @return integer
 */
function tarski_count_authors() {
	global $wpdb;
	$count_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->usermeta WHERE `meta_key` = '" . $wpdb->prefix . "user_level' AND `meta_value` > 1");
	return (int) $count_users;
}

/**
 * tarski_should_show_authors() - Determines whether Tarski should show authors.
 * 
 * @since 2.0.3
 * @global object $wpdb
 * @return boolean
 */
function tarski_should_show_authors() {
	return (bool) (tarski_count_authors() > 1);
}

/**
 * tarski_resave_show_authors() - Re-saves Tarski's 'show_authors' option.
 * 
 * If more than one author is detected, it will turn the 'show_authors'
 * option on; otherwise it will turn it off.
 * @since 2.0.3
 * @see tarski_should_show_authors()
 */
function tarski_resave_show_authors() {
	update_tarski_option('show_authors', tarski_should_show_authors());
}

?>