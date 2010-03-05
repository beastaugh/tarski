<?php

/**
 * detectWPMUadmin() - Detect whether the current user is a WPMU site administrator.
 * 
 * @since 2.0
 * @return boolean
 */
function detectWPMUadmin() {
	return detectWPMU() ? is_site_admin() : false;
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
	if ($file)
		$file = TARSKICACHE . '/' . $file;
	
	$writable = false;
	
	if ($file && file_exists($file))
		$writable = is_writable($file);
	elseif (file_exists(TARSKICACHE))
		$writable = is_writable(TARSKICACHE);
	elseif (is_writable(WP_CONTENT_DIR))
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
	
	$options = flush_tarski_options();
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
	
	$options = flush_tarski_options();
	
	if (!is_numeric($options->deleted) || $options->deleted < 1) {
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
	
	$options = flush_tarski_options();
	
	if (is_numeric($options->deleted) && $options->deleted > 0) {
		unset($options->deleted);
		update_option('tarski_options', $options);
	}
	
	wp_redirect(admin_url('themes.php?page=tarski-options&restored=true'));	
}

/**
 * Wipes Tarski's options if the restoration window has elapsed.
 * 
 * When the user resets Tarski's options, the 'deleted' property on the options
 * object is set to the current time. After three hours have elapsed (during
 * which time the user may restore their options), the tarski_options row in
 * the wp_options table will be deleted entirely by this function.
 * 
 * @see delete_tarski_options()
 * @see restore_tarski_options()
 * @uses flush_tarski_options()
 * @since 2.4
 */
function maybe_wipe_tarski_options() {
	$options = flush_tarski_options();
	$del = $options->deleted;
	
	if (is_numeric($del) && (time() - $del) > (3 * 3600)) {
		delete_option('tarski_options');
		flush_tarski_options();
	}
}

/**
 * Determines whether Tarski needs upgrading.
 * 
 * 'Needs upgrading' is defined as having either no installed version,
 * or having an installed version with a lower version number than the
 * version number extracted from the main stylesheet.
 *
 * @since 2.1
 *
 * @uses get_option
 * @uses get_tarski_option
 * @uses theme_version
 *
 * @return boolean
 */
function tarski_upgrade_needed() {
    if (!get_option('tarski_options')) return false;
    $installed = get_tarski_option('installed');
    return empty($installed) || version_compare($installed, theme_version('current')) === -1;
}

/**
 * tarski_upgrade_and_flush_options() - Upgrades Tarski if needed and flushes options.
 * 
 * @since 2.1
 * @see tarski_upgrade_needed()
 * @see tarski_upgrade()
 * @uses tarski_upgrade_needed()
 * @uses tarski_upgrade()
 * @uses flush_tarski_options
 */
function tarski_upgrade_and_flush_options() {
	if (tarski_upgrade_needed()) {
		tarski_upgrade();
		flush_tarski_options();
	}
}

/**
 * tarski_upgrade_special() - Upgrades Tarski options special cases.
 * 
 * @since 2.3
 * @see tarski_upgrade()
 * @uses tarski_should_show_authors()
 * @param object $options
 * @param object $defaults
 */
function tarski_upgrade_special($options, $defaults) {
	if (tarski_should_show_authors())
		$options->show_authors = true;
	
	if (empty($options->centred_theme) && isset($options->centered_theme))
		$options->centred_theme = true;
	
	if (empty($options->show_categories) && isset($options->hide_categories) && ($options->hide_categories == 1))
		$options->show_categories = false;
}

/**
 * Tarski preferences sometimes change between versions, and need to
 * be updated. This function does not determine whether an update is
 * needed, it merely perfoms it. It's also self-contained, so it
 * won't update the global $tarski_options object either.
 *
 * @since 2.1
 * @uses tarski_upgrade_special
 */
function tarski_upgrade() {
    // Get options and set defaults
    $options = get_option('tarski_options');
    
    // Update the options version so we don't run this code more than once
    $options->installed = theme_version('current');
    
    // Handle special cases first
    tarski_upgrade_special($options, null);
    
    // Save our upgraded options
    update_option('tarski_options', flush_tarski_options());
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
 * Displays the Options page.
 *
 * @since 1.0
 *
 * @uses current_user_can
 * @see WP_Http
 */
function tarski_admin() {
    if (current_user_can('edit_themes')) {
        if (!class_exists('WP_Http')) {
            require_once(ABSPATH . WPINC . '/class-http.php');
        }
        
        tarski_template('options_page.php');
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
 * Adds JavaScript to the Tarski Options page.
 *
 * @since 1.4
 *
 * @uses get_bloginfo
 * @uses wp_enqueue_script
 *
 * @return void
*/
function tarski_inject_scripts() {
    $js_dir = get_bloginfo('template_directory') . '/app/js';
    wp_enqueue_script('page_select', tarski_js("$js_dir/page_select.js"));
    wp_enqueue_script('header_select', tarski_js("$js_dir/header_select.js"));
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
 * @uses tarski_count_authors()
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
 * @uses tarski_should_show_authors()
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
 */
function tarski_navbar_select() {
	$pages = &get_pages('sort_column=post_parent,menu_order');
	$nav_pages = explode(',', get_tarski_option('nav_pages'));
	$collapsed_pages = explode(',', get_tarski_option('collapsed_pages'));
	$walker = new WalkerPageSelect($nav_pages, $collapsed_pages);
	$selector = '';
	
	if (!empty($pages))
		$selector = "<ol id=\"navbar-select\">\n" . $walker->walk($pages, 0, 0, array()) . "\n</ol>\n\n";
	
	if($pages) {
		$navbar_select = '<p>' . __('Pages selected here will display in your navbar.', 'tarski') . "</p>\n"
			. $selector
			. '<input type="hidden" id="opt-collapsed-pages" name="collapsed_pages" value="' . get_tarski_option('collapsed_pages') . '" />'. "\n\n"
			. '<p>' . __('To change the order in which they appear, edit the &#8216;Page Order&#8217; value on each page.', 'tarski') . "</p>\n";
	} else {
		$navbar_select = false;
	}
	
	return $navbar_select;
}

/**
 * Return a list of header images, both from the Tarski directory and the child
 * theme (if one is being used).
 *
 * @uses get_tarski_option
 * @uses get_current_theme
 * @uses get_template_directory_uri
 * @uses get_stylesheet_directory_uri
 *
 * @return array
 */
function _tarski_list_header_images() {
    $headers = array();
    $dirs    = array('Tarski' => TEMPLATEPATH);
    $current = get_tarski_option('header');
    $theme   = get_current_theme();
    
    if (TEMPLATEPATH != STYLESHEETPATH)
        $dirs[$theme] = STYLESHEETPATH;
    
    foreach ($dirs as $theme => $dir) {
        $dirpath = $dir . '/headers';
        
        if (is_dir($dirpath))
            $header_dir = dir($dirpath);
        else
            continue;
        
        while ($file = $header_dir->read()) {
            if (preg_match('/^[^.].+\.(jpg|png|gif)/', $file) &&
                !preg_match('/-thumb\.(jpg|png|gif)$/', $file)) {
                $name = $theme . '/' . $file;
                $id   = 'header_' . preg_replace('/[^a-z_]/', '_', strtolower($name));
                $uri  = ($dir == TEMPLATEPATH
                      ? get_template_directory_uri()
                      : get_stylesheet_directory_uri()) . "/headers/$file";
                $is_current = is_string($current) && $current == $file ||
                              $current[0] == $theme && $current[1] == $file;
                $headers[] = array(
                    'name'    => $name,
                    'id'      => $id,
                    'lid'     => 'for_' . $id,
                    'path'    => $uri,
                    'current' => $is_current,
                    'thumb'   => preg_replace('/(\.(?:png|gif|jpg))/', '-thumb\\1', $uri));
            }
        }
    }
    
    return $headers;
}

/**
 * Return a list of alternate stylesheets, both from the Tarski directory and
 * the child theme (if one is being used).
 *
 * @uses get_tarski_option
 * @uses is_valid_tarski_style
 * @uses get_current_theme
 *
 * @return array
 */
function _tarski_list_alternate_styles() {
    $styles        = array();
    $dirs          = array('Tarski' => TEMPLATEPATH);
    $current_style = get_tarski_option('style');
    $current_theme = get_current_theme();
    
    if (TEMPLATEPATH != STYLESHEETPATH)
        $dirs[$current_theme] = STYLESHEETPATH;
    
    foreach ($dirs as $theme => $dir) {
        $dirpath = $dir . '/styles';
        
        if (is_dir($dirpath))
            $style_dir = dir($dirpath);
        else
            continue;
        
        while ($file = $style_dir->read()) {
            if (is_valid_tarski_style($file)) {
                $is_current = (is_string($current_style)    &&
                               $current_theme == 'Tarski'   &&
                               $current_style == $file)
                            || ($current_style[0] == $theme &&
                                $current_style[1] == $file);
                $prefix = $theme . '/';
                $styles[] = array(
                    'name'    => $prefix . $file,
                    'public'  => ($theme == 'Tarski' ? '' : $prefix) . $file,
                    'current' => $is_current);
            }
        }
    }
    
    return $styles;
}

/**
 * tarski_miscellaneous_options() - Returns a list of checkboxes for miscellaneous options.
 * 
 * Used for a bunch of options that don't really fit anywhere else.
 * @uses tarski_option_checkbox()
 * @since 2.4
 * @return string
 */
function tarski_miscellaneous_options() {
	$output = '';
	$checkboxes = array(
		'display_title' => __('Display site title', 'tarski'),
		'display_tagline' => __('Display site tagline', 'tarski'),
		'show_categories' => __('Show post categories', 'tarski'),
		'tags_everywhere' =>  __('Show tags everywhere', 'tarski'),
		'use_pages' => __('Paginate index pages', 'tarski'),
		'centred_theme' => __('Centrally align the theme', 'tarski'),
		'swap_sides' =>  __('Switch column order', 'tarski'),
		'swap_title_order' => __('Reverse document title order', 'tarski')
	);
	
	foreach($checkboxes as $name => $label)
		$output .= tarski_option_checkbox($name, $label) . "\n\n";
	
	return $output;
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
 * Include an options page template fragment.
 *
 * @since 2.4
 *
 * @uses tarski_template
 *
 * @param string $block
 */
function tarski_options_fragment($block) {
    $block = preg_replace("/\.php$/", "", $block);
    tarski_template("options/$block.php");
}

/**
 * tarski_options_block() - Includes an options page postbox.
 * 
 * @uses tarski_options_fragment()
 * @since 2.4
 * @param string $block
 * @param string $title
 */
function tarski_options_block($block, $title) {
	echo "<div class=\"postbox\"><h3 class=\"hndle\">$title</h3>\n\t<div class=\"inside\">";
	tarski_options_fragment($block);
	echo "\t</div>\n</div>";
}

/**
 * tarski_options_fn_block() - Includes an options page postbox.
 * 
 * @since 2.4
 * @param string $block
 * @param string $title
 */
function tarski_options_fn_block($fn, $title, $args = array()) {
	$fn_output = call_user_func_array($fn, $args);
	if ($fn_output) {
		printf(
			"<div class=\"postbox\"><h3 class=\"hndle\">%s</h3>\n\t<div class=\"inside\">%s\t</div>\n</div>",
			$title, $fn_output
		);
	}
}

/**
 * tarski_option_checkbox() - Returns checkbox markup for a given Tarski option.
 * 
 * @since 2.4
 * @param string $name
 * @param string $label
 * @return string
 */
function tarski_option_checkbox($name, $label) {
	$id = "tarski_option_$name";
	$checked = '';
	
	if(get_tarski_option($name))
		$checked = 'checked="checked" ';
	
	$hidden = "<input type=\"hidden\" name=\"$name\" value=\"0\" />";
	$checkbox = "<input type=\"checkbox\" id=\"$id\" name=\"$name\" value=\"1\" $checked/>";
	
	return sprintf(
		"<label for=\"%s\">\n\t%s\n\t%s\n\t%s\n</label>",
		$id, $hidden, $checkbox, $label
	);
}

?>