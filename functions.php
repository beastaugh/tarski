<?php // functions.php - Tarski functions library

// Path constants
define('TARSKILIB', TEMPLATEPATH.'/library');
define('TARSKICLASSES', TARSKILIB.'/classes');
define('TARSKIHELPERS', TARSKILIB.'/helpers');
define('TARSKIINCLUDES', TARSKILIB.'/includes');
define('TARSKIDISPLAY', TARSKILIB.'/display');
define('TARSKICACHE', TARSKILIB.'/cache');

// Legacy
@include(TEMPLATEPATH."/constants.php");

// Options
global $tarski_options;
flush_tarski_options();

// Warp speed!
include(TARSKICLASSES."/tarski.php");
Tarski::engage();





// serialisation stuff

// update the options array from the database
function flush_tarski_options() {
	global $tarski_options;
	$tarski_options = unserialize(get_option('tarski_options'));
}

function add_tarski_option($name, $value) {
	update_tarski_option($name, $value);
}

function drop_tarski_option($name) {
	update_tarski_option($name, "", true);
}

// get a specific option
function get_tarski_option($name) {
	global $tarski_options;
	return $tarski_options[$name];
}

function tarski_option($name) {
	echo get_tarski_option($name);
}

// update a specific option
function update_tarski_option($name, $value, $drop = false) {
	global $tarski_options;
	$tarski_options[$name] = $value;
	if($drop == true) {
		unset($tarski_options[$name]);
	}
	update_option('tarski_options', serialize($tarski_options));
	flush_tarski_options();
}

// for multiple option updates, can pass an array('name' => 'value'); with all options to save queries
function update_tarski_options($array) {
	global $tarski_options;
	foreach($array as $name => $value) {
		$tarski_options[$name] = $value;
	}
	update_option('tarski_options', serialize($tarski_options));
	flush_tarski_options();
}

// detect WordPress MultiUser - http://mu.wordpress.org/
function detectWPMU() {
	return function_exists('is_site_admin');
}

function install_defaults() {
	
	$options = array(
		'installed' => theme_version(),
		'update_notification' => 'true',
		'blurb' => __('This is the about text','tarski'),
		'sidebar_type' => 'tarski',
		'sidebar_pages' => true,
		'sidebar_links' => true,
		'header' => 'greytree.jpg',
		'display_title' => true,
		'display_tagline' => true,
		'show_categories' => true,
		'centered_theme' => true
	);
	
	update_tarski_options($options);
}

// Update function
// I r serious cat. This r serious function.
function tarskiupdate() {
	global $wpdb, $user_ID;
	get_currentuserinfo();
	
	if ( !empty($_POST) ) {
		if($_POST['update_notification'] == 'off') {
			update_tarski_option('update_notification', false);
		}	elseif($_POST['update_notification'] == 'on') {
			update_tarski_option('update_notification', true);
		}
		
		if (isset($_POST['about_text'])) {
			$about = $_POST['about_text'];
			update_tarski_option('blurb', $about, '','');
		}
		if (isset($_POST['header_image'])) {
			$header = $_POST['header_image'];
			$header = @str_replace("-thumb", "", $header);
			update_tarski_option('header', $header, '','');
		}
		
		$nav_pages = implode(",", $_POST['nav_pages']);
		
		update_tarski_options(array(
			'footer_recent' => $_POST['footer']['recent'],
			'sidebar_pages' => $_POST['sidebar']['pages'],
			'sidebar_links' => $_POST['sidebar']['links'],
			'sidebar_custom' => $_POST['sidebar']['custom'],
			'sidebar_onlyhome' => $_POST['sidebar']['onlyhome'],
			'display_title' => $_POST['display_title'],
			'display_tagline' => $_POST['display_tagline'],
			'show_categories' => $_POST['show_categories'],
			'tags_everywhere' => $_POST['tags_everywhere'],
			'use_pages' => $_POST['use_pages'],
			'centered_theme' => $_POST['centered_theme'],
			'swap_sides' => $_POST['swap_sides'],
			'asidescategory' => $_POST['asides_category'],
			'style' => $_POST['alternate_style'],
			'nav_pages' => $nav_pages,
			'nav_extlinkcat' => $_POST['nav_extlinkcat'],
			'home_link_name' => $_POST['home_link_name'],
			'sidebar_type' => $_POST['sidebartype']
		));
	}
}

// Keeps the navbar order current with the page order
function tarski_resave_navbar() {
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

add_action("save_post","tarski_resave_navbar");

// if we can't find Tarski installed let's go ahead and install all the options that run Tarski. This should run only one more time for all our existing users, then they will just be getting the upgrade function if it exists.
if (!get_tarski_option('installed')) {
	install_defaults();
}

// Here we handle upgrading our users with new options and such. If tarski_installed is in the DB but the version they are running is lower than our current version, trigger this event.
elseif (get_tarski_option('installed') < theme_version()) {
	if(get_tarski_option('installed') < 1.8) {
		if(!get_tarski_option('hide_categories') || get_tarski_option('hide_categories') == '0') {
			add_tarski_option('show_categories', '1');
		}
		drop_tarski_option('hide_categories');
	}
	if(get_tarski_option('installed') < 1.1) {
		add_tarski_option('asidescategory', '0');
	}
	if(!get_tarski_option('update_notification','false') &&!get_tarski_option('update_notification','true')) {
		add_tarski_option('update_notification','true');
	}
	update_tarski_option('installed', theme_version());
}

// ~fin~ ?>