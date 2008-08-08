<?php

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
	if ( $file )
		$cachefile = TARSKICACHE . '/' . $file;
	
	if ( file_exists($cachefile) )
		return is_writable($cachefile);
	else
		return is_writable(TARSKICACHE);
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
		$installed = get_tarski_option('installed');
		return empty($installed) || version_compare($installed, theme_version('current')) === -1;
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
 * tarski_upgrade_special() - Upgrades Tarski options special cases.
 * 
 * @since 2.3
 * @see tarski_upgrade()
 * @param object $options
 * @param object $defaults
 */
function tarski_upgrade_special($options, $defaults) {
	if ( tarski_should_show_authors() )
		$options->show_authors = true;
	
	if ( empty($options->centred_theme) && isset($options->centered_theme) )
		$options->centred_theme = true;
	
	if ( empty($options->show_categories) && isset($options->hide_categories) && ($options->hide_categories == 1) )
		$options->show_categories = false;
	
}

/**
 * tarski_upgrade_widgets() - Upgrades old Tarski sidebar options to use widgets.
 * 
 * @since 2.3
 * @see tarski_upgrade()
 * @param object $options
 * @param object $defaults
 */
function tarski_upgrade_widgets($options, $defaults) {
	$widgets = wp_get_sidebars_widgets(false);
	$widget_text = get_option('widget_text');
	
	// Change sidebar names and initialise new sidebars
	if ( empty($widgets['sidebar-main']) && !empty($widgets['sidebar-1']) )
		$widgets['sidebar-main'] = $widgets['sidebar-1'];
	
	if ( empty($widgets['footer-sidebar']) && !empty($widgets['sidebar-2']) )
		$widgets['footer-sidebar'] = $widgets['sidebar-2'];
	
	// Main footer widgets
	if ( empty($widgets['footer-main']) ) {
		$widgets['footer-main'] = array();
		
		// Footer blurb
		if ( strlen(trim($options->blurb)) ) {
			$widget_text[] = array( 'title' => '', 'text' => $options->blurb );
			$wt_num = (int) end(array_keys($widget_text));
			$widgets['footer-main'][] = "text-$wt_num";
		}
		
		// Recent articles
		if ( $options->footer_recent )
			$widgets['footer-main'][] = 'recent-articles';
	}
	
	// Main sidebar
	if ( empty($widgets['sidebar-main']) && $options->sidebar_type == 'tarski' ) {
		$widgets['sidebar-main'] = array();
	
		// Custom text -> text widget
		if( strlen(trim($options->sidebar_custom)) ) {
			$widget_text[] = array( 'title' => '', 'text' => $options->sidebar_custom );
			$wt_num = (int) end(array_keys($widget_text));
			$widgets['sidebar-main'][] = "text-$wt_num";
		}
	
		// Pages list -> pages widget
		if($options->sidebar_pages)
			$widgets['sidebar-main'][] = 'pages';
	
		// Links list -> links widget
		if($options->sidebar_links)
			$widgets['sidebar-main'][] = 'links';
	}
	
	// Update options
	update_option('widget_text', $widget_text);
	wp_set_sidebars_widgets($widgets);	
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

	// Update the options version so we don't run this code more than once
	$options->installed = theme_version('current');
	
	// Handle special cases first
	tarski_upgrade_special($options, $defaults);
		
	// Upgrade old display options to use widgets instead
	tarski_upgrade_widgets($options, $defaults);
	
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
	update_option('tarski_options', $options);
}

/**
 * tarski_messages() - Adds messages about Tarski to the WordPress admin panel.
 * 
 * @since 2.1
 * @hook filter tarski_messages
 * Filter the messages Tarski prints to the WordPress admin panel.
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
	if (current_user_can('edit_themes')) {
		save_tarski_options();
		tarski_update_notifier('options_page');
		$widgets_link = admin_url('widgets.php');
		$tarski_options_link = admin_url('themes.php?page=tarski-options');
		include(TARSKIDISPLAY . '/options_page.php');
	}
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
	wp_enqueue_style(
		'tarski_admin',
		get_bloginfo('template_directory') . '/library/css/admin.css',
		array(), false, 'screen'
	);
}

/**
 * tarski_inject_styles() - Adds CSS to the Tarski Options page.
 * 
 * @since 2.1
*/
function tarski_inject_styles() {
	wp_enqueue_style(
		'tarski_options',
		get_bloginfo('template_directory') . '/library/css/options.css',
		array(), false, 'screen'
	);
}

/**
 * tarski_inject_scripts() - Adds JavaScript to the Tarski Options page.
 * 
 * @since 1.4
*/
function tarski_inject_scripts() {
	$js_dir = get_bloginfo('template_directory') . '/app/js';
	wp_enqueue_script('page_select', "$js_dir/page_select.js");
	wp_enqueue_script('crir', "$js_dir/crir.js");
}

/**
 * tarski_count_authors() - Returns the number of authors who have published posts.
 * 
 * This function returns the number of author ids associated with published posts.
 * @since 2.0.3
 * @global object $wpdb
 * @return integer
 */
function tarski_count_authors() {
	global $wpdb;
	return count($wpdb->get_col($wpdb->prepare(
		"SELECT post_author, COUNT(DISTINCT post_author) FROM $wpdb->posts WHERE post_status = 'publish' GROUP BY post_author"
	), 1));
}

/**
 * tarski_should_show_authors() - Determines whether Tarski should show authors.
 * 
 * @since 2.0.3
 * @see tarski_count_authors()
 * @global object $wpdb
 * @return boolean
 * @hook filter tarski_show_authors
 * Allows other components to decide whether or not Tarski should show authors.
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
function tarski_navbar_select($pages) {
	$nav_pages = explode(',', get_tarski_option('nav_pages'));
	$collapsed_pages = explode(',', get_tarski_option('collapsed_pages'));
	$walker = new WalkerPageSelect($nav_pages, $collapsed_pages);
	$return = '';
	
	if ( !empty($pages) ) {	
		$return = "<ol id=\"navbar-select\">\n" . $walker->walk($pages, 0, 0, array()) . "\n</ol>\n\n";
	}
	
	return $return;
}

/**
 * tarski_update_notifier() - Performs version checks and outputs the update notifier.
 * 
 * Creates a new Version object, checks the latest and current
 * versions, and lets the user know whether or not their version
 * of Tarski needs updating. The way it displays varies slightly
 * between the WordPress Dashboard and the Tarski Options page.
 * @since 2.0
 * @param string $location
 * @return string
 */
function tarski_update_notifier($messages) {
	global $plugin_page;
	
	if ( !is_array($messages) )
		$messages = array();
	
	$version = new Version;
	$version->current_version_number();
	$svn_link = 'http://tarskitheme.com/help/updates/svn/';
	
	// Update checking only performed when remote files can be accessed
	if ( can_get_remote() ) {
		
		// Only performs the update check when notification is enabled
		if ( get_tarski_option('update_notification') ) {
			$version->latest_version_number();
			$version->latest_version_link();
			$version->version_status();
			
			if ( $version->status == 'older' ) {
				$messages[] = sprintf(
					__('A new version of the Tarski theme, version %1$s %2$s. Your installed version is %3$s.','tarski'),
					"<strong>$version->latest</strong>",
					'<a href="' . $version->latest_link . '">' . __('is now available','tarski') . '</a>',
					"<strong>$version->current</strong>"
				);
			} elseif ( $plugin_page == 'tarski-options' ) {
				switch($version->status) {
					case 'current':
						$messages[] = sprintf(
							__('Your version of Tarski (%s) is up to date.','tarski'),
							"<strong>$version->current</strong>"
						);
					break;
					case 'newer':
						$messages[] = sprintf(
							__('You appear to be running a development version of Tarski (%1$s). Please ensure you %2$s.','tarski'),
							"<strong>$version->current</strong>",
							"<a href=\"$svn_link\">" . __('stay updated','tarski') . '</a>'
						);
					break;
					case 'no_connection':
					case 'error':
						$messages[] = sprintf(
							__('No connection to update server. Your installed version is %s.','tarski'),
							"<strong>$version->current</strong>"
						);
					break;
				}
			}
		} elseif ( $plugin_page == 'tarski-options' ) {
			$messages[] = sprintf(
				__('Update notification for Tarski is disabled. Your installed version is %s.','tarski'),
				"<strong>$version->current</strong>"
			);
		}
	}
	
	return $messages;
}

?>