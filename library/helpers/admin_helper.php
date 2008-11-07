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
 * cache_is_writable() - Checks whether WordPress can write to $file in Tarski's cache directory.
 * 
 * If $file isn't given, the function checks to see if new files can 
 * be written to the cache directory, and attempts to create the cache
 * directory if it isn't present.
 * @since 1.7
 * @param string $file
 * @return boolean
 */
function cache_is_writable($file = false) {
	if ( $file )
		$file = TARSKICACHE . '/' . $file;
	
	$writable = false;
	
	if ( $file && file_exists($file) )
		$writable = is_writable($file);
	elseif ( file_exists(TARSKICACHE) )
		$writable = is_writable(TARSKICACHE);
	elseif ( is_writable(WP_CONTENT_DIR) )
		$writable = wp_mkdir_p(TARSKICACHE);

	return $writable;
}

/**
 * save_tarski_options() - Saves a new set of Tarski options.
 * 
 * The primary request handler for the Tarski options system. Saves any updated
 * options and redirects to the options page.
 * 
 * @see tarskiupdate() which it replaces
 * @see delete_tarski_options()
 * @see restore_tarski_options()
 * @since 2.0
 */
function save_tarski_options() {
	check_admin_referer('admin_post_tarski_options', '_wpnonce_tarski_options');
	
	if (!current_user_can('edit_themes'))
		wp_die(__('You are not authorised to perform this operation.', 'tarski'));
	
	$options = new Options;
	$options->tarski_options_get();
		
	$options->tarski_options_update();
	update_option('tarski_options', $options);
	
	wp_redirect(admin_url('themes.php?page=tarski-options&updated=true'));
}

/**
 * delete_tarski_options() - Sets the 'deleted' property on Tarski's options.
 * 
 * A secondary request handler for the Tarski options system. Sets the
 * 'deleted' property in the options object to the current time and redirects
 * to the options page.
 * 
 * @see save_tarski_options()
 * @see restore_tarski_options()
 * @see maybe_wipe_tarski_options()
 * @since 2.4
 */
function delete_tarski_options() {
	check_admin_referer('admin_post_delete_tarski_options', '_wpnonce_delete_tarski_options');
	
	if (!current_user_can('edit_themes'))
		wp_die(__('You are not authorised to perform this operation.', 'tarski'));
	
	$options = new Options;
	$options->tarski_options_get();
	
	if (!is_int($options->deleted) || $options->deleted < 1) {
		$options->deleted = time();
		update_option('tarski_options', $options);
	}
	
	wp_redirect(admin_url('themes.php?page=tarski-options&deleted=true'));
}

/**
 * restore_tarski_options() - Unsets the 'deleted' property on Tarski's options.
 * 
 * A secondary request handler for the Tarski options system. Unsets the
 * 'deleted' property in the options object and redirects to the options page.
 * 
 * @see save_tarski_options()
 * @see delete_tarski_options()
 * @since 2.4
 */
function restore_tarski_options() {
	check_admin_referer('admin_post_restore_tarski_options', '_wpnonce_restore_tarski_options');
	
	if (!current_user_can('edit_themes'))
		wp_die(__('You are not authorised to perform this operation.', 'tarski'));
	
	$options = new Options;
	$options->tarski_options_get();
	
	if (is_int($options->deleted) && $options->deleted > 0) {
		unset($options->deleted);
		update_option('tarski_options', $options);
	}
	
	wp_redirect(admin_url('themes.php?page=tarski-options&restored=true'));	
}

/**
 * maybe_wipe_tarski_options() - Wipes Tarski's options if the restoration window has elapsed.
 * 
 * When the user resets Tarski's options, the 'deleted' property on the options
 * object is set to the current time. After three hours have elapsed (during
 * which time the user may restore their options), the tarski_options row in
 * the wp_options table will be deleted entirely by this function.
 * 
 * @see delete_tarski_options()
 * @see restore_tarski_options()
 * @since 2.4
 */
function maybe_wipe_tarski_options() {
	$options = new Options;
	$options->tarski_options_get();
	$del = $options->deleted;
	
	if (is_int($del) && (time() - $del) > (3 * 3600)) {
		delete_option('tarski_options');
		flush_tarski_options();
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
		flush_tarski_options();
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
 * tarski_prefill_sidebars() - Default content for Tarski's widget areas.
 * 
 * Should leave existing sidebars well alone, and be compatible with the
 * Tarski upgrade process.
 * @since 2.4
 */
function tarski_prefill_sidebars() {
	$widgets = wp_get_sidebars_widgets(false);
	
	if (!array_key_exists('sidebar-main', $widgets))
		if (array_key_exists('sidebar-1', $widgets))
			$widgets['sidebar-main'] = $widgets['sidebar-1'];
		else
			$widgets['sidebar-main'] = array('categories', 'links');
	
	if (!array_key_exists('footer-sidebar', $widgets))
		if (array_key_exists('sidebar-2', $widgets))
			$widgets['footer-sidebar'] = $widgets['sidebar-2'];
		else
			$widgets['footer-sidebar'] = array('search');
	
	if (!array_key_exists('footer-main', $widgets))
		$widgets['footer-main'] = array(__('Recent Articles','tarski'));
	
	wp_set_sidebars_widgets($widgets);
}

/**
 * tarski_addmenu() - Adds the Tarski Options page to the WordPress admin panel.
 * 
 * @since 1.0
 */
function tarski_addmenu() {
	$page = add_theme_page(
		__('Tarski Options','tarski'),
		__('Tarski Options','tarski'),
		'edit_themes',
		'tarski-options',
		'tarski_admin'
	);
	
	add_action("admin_print_scripts-$page", 'tarski_inject_scripts');
	add_action("admin_print_styles-$page", 'tarski_inject_styles');
}

/**
 * tarski_admin() - Displays the Options page.
 * 
 * @since 1.0
 */
function tarski_admin() {
	if (current_user_can('edit_themes'))
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
	wp_enqueue_script('header_select', "$js_dir/header_select.js");
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
 * Creates a new TarskiVersion object, checks the latest and current
 * versions, and lets the user know whether or not their version
 * of Tarski needs updating. The way it displays varies slightly
 * between the WordPress Dashboard and the Tarski Options page.
 * @since 2.0
 * @param string $location
 * @return string
 */
function tarski_update_notifier() {
	$version = new TarskiVersion();
	return $version->status_message();
}

/**
 * tarski_options_fragment() - Includes an options page template fragment.
 * 
 * @since 2.4
 * @param string $block
 */
function tarski_options_fragment($block) {
	$block = preg_replace("/\.php$/", "", $block);
	include(TARSKIDISPLAY . "/options/$block.php");
}

/**
 * tarski_options_block() - Includes an options page postbox.
 * 
 * @see tarski_options_fragment()
 * @since 2.4
 * @param string $block
 * @param string $title
 */
function tarski_options_block($block, $title) {
	echo "<div class=\"postbox\"><h3 class=\"hndle\">$title</h3>\n\t<div class=\"inside\">";
	tarski_options_fragment($block);
	echo "\t</div>\n</div>";
}

?>