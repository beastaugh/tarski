<?php

/**
 * check_input() - Checks input is of correct type
 * 
 * Always returns true when WP_DEBUG is true, to allow for easier debugging
 * in the development environment while handling erroneous input more
 * robustly in the production environment.
 * @see http://uk3.php.net/manual/en/function.gettype.php
 * @since 2.1
 * @param mixed $input
 * @param string $type
 * @param string $name
 * @return boolean
 *
 */
function check_input($input, $type, $name = '') {
	if ( WP_DEBUG === true )
		return true;

	if ( $type == 'object' && strlen($name) > 0 )
		return is_a($input, $name);
	else
		return call_user_func("is_$type", $input);
}

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
	if(detectWPMU()) {
		return is_site_admin();
	}
}

/**
 * can_get_remote() - Detects whether Tarski can download remote files.
 * 
 * Checks if either allow_url_fopen is set or libcurl is available.
 * Mainly used by the update notifier to ensure Tarski only attempts to
 * use available functionality.
 * @since 2.0.3
 * @return boolean
 */
function can_get_remote() {
	return (bool) (function_exists('curl_init') || ini_get('allow_url_fopen'));
}

/**
 * cache_is_writable() - Checks if WordPress can write to $file in Tarski's cache directory.
 * 
 * If $file isn't given, the function checks to see if new files can 
 * be written to the cache directory.
 * @since 1.7
 * @param string $file
 * @return boolean
 */
function cache_is_writable($file = false) {
	if($file)
		$cachefile = TARSKICACHE . '/' . $file;
	
	if(is_writable($cachefile) || (is_writable(TARSKICACHE) && !file_exists($cachefile)))
		return true;
}

/**
 * version_to_integer() - Turns Tarski version numbers into integers.
 * 
 * @since 2.0.3
 * @param string $version
 * @return integer
 */
function version_to_integer($version) {
	// Remove all non-numeric characters
	$version = preg_replace('/\D/', '', $version);
	
	if($version && strlen($version) >= 1) {
		// Make the string exactly three characters (numerals) long
		if(strlen($version) < 2) {
			$version_int = $version . '00';
		} elseif(strlen($version) < 3) {
			$version_int = $version . '0';
		} elseif(strlen($version) == 3) {
			$version_int = $version;
		} elseif(strlen($version) > 3) {
			$version_int = substr($version, 0, 3);
		}
		
		// Return an integer
		return (int) $version_int;
	}
}

/**
 * version_newer_than() - Returns true if current version is greater than given version.
 *
 * @since 2.0.3
 * @param mixed $version
 * @return boolean
 */
function version_newer_than($version) {
	$version = version_to_integer($version);
	$current = version_to_integer(theme_version('current'));
	
	if($version && $current) {
		return (bool) ($current > $version);
	}
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
 * ready_to_delete_options() - Returns true if Tarski is ready to delete its options.
 * 
 * When options are deleted, the time of deletion is saved in Tarski's
 * options. This function checks that time against the current time:
 * if the current time minus the saved time is greater than three hours
 * (i.e. if more than two hours have elapsed since the options were
 * deleted) then this function will return true.
 * @since 2.0.5
 * @return boolean
 */
function ready_to_delete_options($del_time) {
	if(!empty($del_time)) {
		$del_time = (int) $del_time;
		return (bool) (time() - $del_time) > (3 * 3600);
	}
}

/**
 * tarski_upgrade_needed() - Returns true if Tarski needs upgrading.
 * 
 * 'Needs upgrading' is defined as having either no installed version,
 * or having an installed version with a lower version number than the
 * version number extracted from the main stylesheet.
 * @since 2.1
 * @return boolean
 */
function tarski_upgrade_needed() {
	if ( get_option('tarski_options') ) {
		$version = get_tarski_option('installed');
		$current = theme_version('current');
		return (bool) empty($version) || (version_to_integer($version) < version_to_integer($current));
	}
}

/**
 * tarski_upgrade_and_flush_options() - Upgrades Tarski if needed and flushes options.
 * 
 * @since 2.1
 * @see tarski_upgrade_needed()
 * @see tarski_upgrade()
 */
function tarski_upgrade_and_flush_options() {
	if ( tarski_upgrade_needed() ) {
		tarski_upgrade();
		$tarski_options = new Options;
		$tarski_options->tarski_options_get();
	}
}

/**
 * function tarski_upgrade() - Upgrades Tarski's options where appropriate.
 * 
 * Tarski preferences sometimes change between versions, and need to
 * be updated. This function does not determine whether an update is
 * needed, it merely perfoms it. It's also self-contained, so it
 * won't update the global $tarski_options object either.
 * @since 2.1
 */
function tarski_upgrade() {
	// Get existing options
	$options = new Options;
	$options->tarski_options_get();
	
	// Get our defaults, so we can merge them in
	$defaults = new Options;
	$defaults->tarski_options_defaults();

	// Handle special cases first
	
	// Update the options version so we don't run this code more than once
	$options->installed = theme_version('current');
	
	// If they had hidden the sidebar previously for non-index pages, preserve that setting
	if(empty($options->sidebar_pp_type) && isset($options->sidebar_onlyhome) && $options->sidebar_onlyhome == 1) {
		$options->sidebar_pp_type = 'none';
	}
	
	// If there's more than one author, show authors
	if(tarski_should_show_authors()) {
		$options->show_authors = true;
	}
	
	// If categories are hidden, respect that option
	if(empty($options->show_categories) && isset($options->hide_categories) && ($options->hide_categories == 1)) {
		$options->show_categories = false;
	}
	
	// Change American English to British English, sorry Chris
	if(empty($options->centred_theme) && isset($options->centered_theme)) {
		$options->centred_theme = true;
	}
	
	// Upgrade old display options to use widgets instead
	
	// Get current widgets settings
	$widgets = wp_get_sidebars_widgets();
	$widget_text = get_option('widget_text');
	
	// Change sidebar names and initialise new sidebars
	$widgets['sidebar-main'] = $widgets['sidebar-1'];
	$widgets['footer-sidebar'] = $widgets['sidebar-2'];
	$widgets['footer-main'] = array();
	
	// Footer blurb
	if ( strlen(trim($options->blurb)) ) {
		$widget_text[] = array( 'title' => '', 'text' => $options->blurb );
		$wt_num = (int) end(array_keys($widget_text));
		$widgets['footer-main'][] = "text-$wt_num";
	}
	
	// Recent articles
	if ( $options->footer_recent ) {
		$widgets['footer-main'][] = 'recent-articles';
	}
	
	// Footer sidebar default
	if ( empty($widgets['footer-sidebar']) ) {
		$widgets['footer-sidebar'] = array('search');
	}
		
	// Main sidebar
	if ( $options->sidebar_type == 'tarski' ) {
		if ( empty($widgets['sidebar-main']) )
			$widgets['sidebar-main'] = array();
		
		// Custom text -> text widget
		if( strlen(trim($options->sidebar_custom)) ) {
			$widget_text[] = array( 'title' => '', 'text' => $options->sidebar_custom );
			$wt_num = (int) end(array_keys($widget_text));
			$widgets['sidebar-main'][] = "text-$wt_num";
		}
		
		// Pages list -> pages widget
		if($options->sidebar_pages) {
			$widgets['sidebar-main'][] = 'pages';
		}
		
		// Links list -> links widget
		if($options->sidebar_links) {
			$widgets['sidebar-main'][] = 'links';
		}
	}
		
	// Unset defunct values
	unset($widgets['sidebar-1'], $widgets['sidebar-2']);
	
	// Conform our options to the expected values, types, and defaults
	foreach($options as $name => $value) {
		if(!isset($defaults->$name)) {
			// Get rid of options which no longer exist
			unset($options->$name);
		} elseif(!isset($options->$name)) {
			// Use the default if we don't have this option
			$options->$name = $defaults->$name;
		} elseif(is_array($options->$name) && !is_array($defaults->$name)) {
			// If our option is an array and the default is not, implode using " " as a separator
			$options->$name = implode(" ", $options->$name);
		} elseif(!is_array($options->$name) && is_array($defaults->$name)) {
			// If our option is a scalar and the default is an array, wrap our option in an array
			$options->$name = array($options->$name);
		}
	}

	// Save our upgraded options
	update_option('widget_text', $widget_text);
	wp_set_sidebars_widgets($widgets);
	update_option('tarski_options', serialize($options));
}

/**
 * tarski_messages() - Adds messages about Tarski to the WordPress admin panel.
 * 
 * @since 2.1
 */
function tarski_messages() {
	$messages = apply_filters('tarski_messages', array());
	
	foreach ( $messages as $message ) {
		echo "<p class=\"tarski-message\">$message</p>\n\n";
	}
}

/**
 * tarski_addmenu() - Adds the Tarski Options page to the WordPress admin panel.
 * 
 * @since 1.0
 */
function tarski_addmenu() {
	add_theme_page(__('Tarski Options','tarski'), __('Tarski Options','tarski'), 'edit_themes', 'tarski-options', 'tarski_admin');
}

/**
 * tarski_admin() - Saves Tarski's options, and displays the Options page.
 * 
 * @since 1.0
 */
function tarski_admin() {
	save_tarski_options();
	tarski_update_notifier('options_page');
	$widgets_link = get_bloginfo('wpurl') . '/wp-admin/widgets.php';
	$tarski_options_link = get_bloginfo('wpurl') . '/wp-admin/themes.php?page=tarski-options';
	include(TARSKIDISPLAY . '/options_page.php');
}

/**
 * tarski_admin_header_style() - Styles the custom header image admin page for use with Tarski.
 * 
 * @since 1.4
 */
function tarski_admin_header_style() { ?>
	<style type="text/css">
	#headimg {
		height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
		width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
	}
	#headimg h1, #headimg #desc {
		display: none;
	}
	</style>
<?php }

/**
 * tarski_admin_style() - Tarski CSS for the WordPress admin panel.
 * 
 * @since 2.1
*/
function tarski_admin_style() {
	echo '<link rel="stylesheet" href="' . get_bloginfo('template_directory'). '/library/css/admin.css" type="text/css" media="all" />';
}

/**
 * tarski_inject_styles() - Adds CSS to the Tarski Options page.
 * 
 * @since 2.1
*/
function tarski_inject_styles() {
	echo '<link rel="stylesheet" href="' . get_bloginfo('template_directory'). '/library/css/options.css" type="text/css" media="screen" />';
}

/**
 * tarski_inject_scripts() - Adds JavaScript to the Tarski Options page.
 * 
 * @since 1.4
*/
function tarski_inject_scripts() {
	$js_dir = get_bloginfo('template_directory') . '/library/js';
	wp_enqueue_script('crir', $js_dir . '/crir.js');
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
 * @see tarski_count_authors()
 * @global object $wpdb
 * @return boolean
 */
function tarski_should_show_authors() {
	$show_authors = tarski_count_authors() > 1;
	return (bool) apply_filters('tarski_show_authors', $show_authors);
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
	if(get_option('tarski_options')) {
		update_tarski_option('show_authors', tarski_should_show_authors());
	}
}

/**
 * tarski_navbar_select() - Generates a list of checkboxes for the site's pages.
 * 
 * Walks the tree of pages and generates nested ordered lists of pages, with
 * corresponding checkboxes to allow the selection of pages for the navbar.
 * @since 2.2
 * @param array $pages
 * @param array $selected
 */
function tarski_navbar_select($pages, $selected) {
	if ( !empty($pages) ) {
		$walker = new WalkerPageSelect($selected);
		return "<ol id=\"navbar-select\">\n" . $walker->walk($pages, 0, 0, array()) . "\n</ol>\n\n";
	}
}

?>