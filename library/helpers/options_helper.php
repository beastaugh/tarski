<?php

function tarski_inject_scripts() {
	if(substr($_SERVER['REQUEST_URI'], -39, 39) == 'wp-admin/themes.php?page=tarski-options') { // Hack detects Tarski Options page
		include(TEMPLATEPATH."/library/display/options/scripts.php");
	}
}
	
function tarski_addmenu() {
	add_submenu_page('themes.php', __('Tarski Options','tarski'), __('Tarski Options','tarski'), 'edit_themes', 'tarski-options', 'tarski_admin');
}

function tarski_admin() {
	include(TEMPLATEPATH."/library/display/options/main.php");
}

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

add_action('admin_menu', 'tarski_addmenu');
add_action('admin_head', 'tarski_inject_scripts');
add_action("save_post","tarski_resave_navbar");

?>