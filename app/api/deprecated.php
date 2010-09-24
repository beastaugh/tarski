<?php
/**
 * @package WordPress
 * @subpackage Tarski
 *
 * The scrapyard: deprecated functions that haven't yet been removed.
 *
 * Don't write plugins that rely on these functions, as they are liable
 * to be removed between versions. There will usually be a better way
 * to do what you want; post on the forum if you need help.
 * @link http://tarskitheme.com/forum/
 */

/**
 * tarski_searchform() - Outputs the WordPress search form.
 *
 * Will only output the search form on pages that aren't a search
 * page or a 404, as these pages include the search form earlier
 * in the document and the search form relies on the 's' id value,
 * which as an HTML id must be unique within the document.
 * @since 2.0
 * @deprecated 2.8
 */
function tarski_searchform() {
    _deprecated_function(__FUNCTION__, '2.8');
    
    get_search_form();
}

/**
 * detectWPMUadmin() - Detect whether the current user is a WPMU site administrator.
 *
 * @since 2.0
 * @deprecated 2.8
 * @return boolean
 */
function detectWPMUadmin() {
    _deprecated_function(__FUNCTION__, '2.8');
    
    return is_multisite() && is_super_admin();
}

/**
 * Detect whether WordPress Multi-User is in use.
 *
 * @since 1.4
 * @deprecated 2.8
 * @return boolean
 */
function detectWPMU() {
    _deprecated_function(__FUNCTION__, '2.8');
    
    return function_exists('is_site_admin');
}

/**
 * Returns the classes that should be applied to the document body.
 *
 * @since 1.2
 * @deprecated 2.8
 *
 * @uses get_tarski_option
 * @uses is_valid_tarski_style
 * @uses get_bloginfo
 * @uses apply_filters
 *
 * @param boolean $return
 * @return string $classes
 *
 * @hook filter tarski_bodyclass
 * Filter the classes applied to the document body by Tarski.
 */
function tarski_bodyclass($return = false) {
    _deprecated_function(__FUNCTION__, '2.8');
    
    if (get_tarski_option('centred_theme'))
        $classes[] = 'centre';
    
    if (get_tarski_option('swap_sides'))
        $classes[] = 'janus';
    
    if (get_tarski_option('style')) {
        $style = get_tarski_option('style');
        $file  = is_array($style) ? $style[1] : $style;
        
        if (is_valid_tarski_style($file))
            $classes[] = preg_replace('/^(.+)\.css$/', '\\1', $file);
    }
    
    if (get_bloginfo('text_direction') == 'rtl')
        $classes[] = 'rtl';
    
    $classes = apply_filters('tarski_bodyclass', $classes);
    $classes = is_array($classes) ? implode(' ', $classes) : '';
    
    if ($return)
        return $classes;
    else
        echo $classes;
}

/**
 * Upgrade old Tarski sidebar options to use widgets.
 *
 * @since 2.3
 * @deprecated 2.6
 * @see tarski_upgrade
 * @param object $options
 * @param object $defaults
 */
function tarski_upgrade_widgets($options, $defaults) {
    _deprecated_function(__FUNCTION__, '2.6');
    
    $widgets = wp_get_sidebars_widgets(false);
    $widget_text = get_option('widget_text');

    // Change sidebar names and initialise new sidebars
    if (empty($widgets['sidebar-main']) && !empty($widgets['sidebar-1']))
        $widgets['sidebar-main'] = $widgets['sidebar-1'];

    if (empty($widgets['footer-sidebar']) && !empty($widgets['sidebar-2']))
        $widgets['footer-sidebar'] = $widgets['sidebar-2'];

    // Main footer widgets
    if (empty($widgets['footer-main'])) {
        $widgets['footer-main'] = array();

        // Footer blurb
        if (isset($options->blurb) && strlen(trim($options->blurb))) {
            $widget_text[] = array( 'title' => '', 'text' => $options->blurb );
            $wt_num = (int) end(array_keys($widget_text));
            $widgets['footer-main'][] = "text-$wt_num";
        }

        // Recent articles
        if (isset($options->footer_recent) && $options->footer_recent)
            $widgets['footer-main'][] = 'recent-articles';
    }

    // Main sidebar
    if (empty($widgets['sidebar-main']) && isset($options->sidebar_type) && $options->sidebar_type == 'tarski') {
        $widgets['sidebar-main'] = array();

        // Custom text -> text widget
        if(isset($options->sidebar_custom) && strlen(trim($options->sidebar_custom))) {
            $widget_text[] = array( 'title' => '', 'text' => $options->sidebar_custom );
            $wt_num = (int) end(array_keys($widget_text));
            $widgets['sidebar-main'][] = "text-$wt_num";
        }

        // Pages list -> pages widget
        if (isset($options->sidebar_pages) && $options->sidebar_pages)
            $widgets['sidebar-main'][] = 'pages';

        // Links list -> links widget
        if(isset($options->sidebar_links) && $options->sidebar_links)
            $widgets['sidebar-main'][] = 'links';
    }

    // Update options
    update_option('widget_text', $widget_text);
    wp_set_sidebars_widgets($widgets);
}

/**
 * check_input() - Checks input is of correct type
 * 
 * Always returns true when WP_DEBUG is true, to allow for easier debugging
 * in the development environment while handling erroneous input more
 * robustly in the production environment.
 * @see http://uk3.php.net/manual/en/function.gettype.php
 * @since 2.1
 * @deprecated 2.5
 * @param mixed $input
 * @param string $type
 * @param string $name
 * @return boolean
 *
 */
function check_input($input, $type, $name = '') {
	_deprecated_function(__FUNCTION__, '2.5');
	
	if ( defined('WP_DEBUG') && WP_DEBUG === true )
		return true;

	if ( $type == 'object' && strlen($name) > 0 )
		return is_a($input, $name);
	else
		return call_user_func("is_$type", $input);
}

/**
 * Recent entries á la Tarski.
 *
 * Lists the five most recent entries, or, on the home page, the five most
 * recent entries after those posts actually displayed on the page.
 *
 * @since 2.0.5
 * @deprecated 2.5
 *
 * @see wp_widget_recent_entries
 * @uses wp_cache_get
 * @uses wp_cache_add
 * @uses wp_reset_query
 *
 * @global object $posts
 * @return string
 */
function tarski_recent_entries($args = array()) {
	_deprecated_function(__FUNCTION__, '2.5');

	global $posts;

	$output = wp_cache_get('tarski_recent_entries');

	if (strlen($output)) {
		echo $output;
		return;
	}

	ob_start();
	extract($args);

	$options = array();
	$title = empty($options['title']) ? __('Recent Articles', 'tarski') : $options['title'];
	$number = (array_key_exists('number', $options)) ? intval($options['number']) : 5;

	if ($number < 1)
		$number = 1;
	elseif ($number > 10)
		$number = 10;

	$recent = new WP_Query(array(
		'showposts' => $number,
		'what_to_show' => 'posts',
		'nopaging' => 0,
		'post_status' => 'publish',
		'offset' => (is_home()) ? count($posts) : 0));

	if ($recent->have_posts()) {
?>
<div id="recent">
	<?php echo $before_title . $title . $after_title; ?>
	<ul>
		<?php while ($recent->have_posts()) { $recent->the_post(); ?>
		<li>
			<h4 class="recent-title"><a title="<?php _e('View this post', 'tarski'); ?>" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h4>
			<p class="recent-metadata"><?php printf(get_tarski_option('show_categories') ? __('%1$s in %2$s', 'tarski') : '%s',
				the_time(get_option('date_format')),
				get_the_category_list(', ', '', false)); ?></p>
			<div class="recent-excerpt content"><?php the_excerpt(); ?></div>
		</li>
		<?php } ?>
	</ul>
</div> <!-- /recent -->
<?php
		unset($recent);
		wp_reset_query();  // Restore global post data stomped by the_post().
	}

	wp_cache_add('tarski_recent_entries', ob_get_flush(), 'widget');
}

/**
 * flush_tarski_recent_entries() - Deletes tarski_recent_entries() from the cache.
 *
 * @since 2.0.5
 * @deprecated 2.5
 *
 * @see tarski_recent_entries()
 */
function flush_tarski_recent_entries() {
	_deprecated_function(__FUNCTION__, '2.5');

	wp_cache_delete('tarski_recent_entries');
}

/**
 * Default content for Tarski's widget areas.
 *
 * Should leave existing sidebars well alone, and be compatible with the
 * Tarski upgrade process. Deprecated since it's not compatible with
 * WordPress 2.8's widgets implementation.
 *
 * @since 2.4
 * @deprecated 2.5
 */
function tarski_prefill_sidebars() {
	_deprecated_function(__FUNCTION__, '2.5');
	
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
		$widgets['footer-main'] = array('recent-articles');
	
	wp_set_sidebars_widgets($widgets);
}

/**
 * add_version_to_styles() - Adds version number to style links.
 *
 * This makes browsers re-download the CSS file when the version
 * number changes, reducing problems that may occur when markup
 * changes but the corresponding new CSS is not downloaded.
 * @since 2.0.1
 * @deprecated 2.5
 *
 * @see tarski_stylesheets()
 * @param array $style_array
 * @return array $style_array
 */
function add_version_to_styles($style_array) {
	_deprecated_function(__FUNCTION__, '2.5');
	
	if(check_input($style_array, 'array')) {
		foreach($style_array as $type => $values) {
			if(is_array($values) && $values['url']) {
				$style_array[$type]['url'] .= '?v=' . theme_version();
			}
		}
	}
	return $style_array;
}

/**
 * generate_feed_link() - Returns a properly formatted RSS or Atom feed link
 *
 * @since 2.1
 * @deprecated 2.5
 *
 * @param string $title
 * @param string $link
 * @param string $type
 * @return string
 */
function generate_feed_link($title, $link, $type = '') {
	if (function_exists('feed_content_type'))
		_deprecated_function(__FUNCTION__, '2.5');
	
	if ( $type == '' )
		$type = feed_link_type();

	return "<link rel=\"alternate\" type=\"$type\" title=\"$title\" href=\"$link\" />";
}

/**
 * feed_link_type() - Returns an Atom or RSS feed MIME type
 *
 * @since 2.1
 * @deprecated 2.5
 *
 * @param string $type
 * @return string
 */
function feed_link_type($type = '') {
	if (function_exists('feed_content_type'))
		_deprecated_function(__FUNCTION__, '2.5', feed_content_type($type));
	
	if(empty($type))
		$type = get_default_feed();

	if($type == 'atom')
		return 'application/atom+xml';
	else
		return 'application/rss+xml';
}

/**
 * is_wp_front_page() - Returns true when current page is the WP front page.
 * 
 * Very useful, since is_home() doesn't return true for the front page
 * if it's displaying a static page rather than the usual posts page.
 * @since 2.0
 * @deprecated 2.4
 * @return boolean
 */
function is_wp_front_page() {
	_deprecated_function(__FUNCTION__, '2.4', is_front_page());

	if(get_option('show_on_front') == 'page')
		return is_page(get_option('page_on_front'));
	else
		return is_home();
}

/**
 * can_get_remote() - Detects whether Tarski can download remote files.
 * 
 * Checks if either allow_url_fopen is set or libcurl is available.
 * Mainly used by the update notifier to ensure Tarski only attempts to
 * use available functionality.
 * @since 2.0.3
 * @deprecated 2.4
 * @return boolean
 */
function can_get_remote() {
	_deprecated_function(__FUNCTION__, '2.4');
	
	return (bool) (function_exists('curl_init') || ini_get('allow_url_fopen'));
}

/**
 * tarski_admin_style() - Tarski CSS for the WordPress admin panel.
 * 
 * @since 2.1
 * @deprecated 2.4
 */
function tarski_admin_style() {
	_deprecated_function(__FUNCTION__, '2.4');
	
	wp_enqueue_style(
		'tarski_admin',
		get_bloginfo('template_directory') . '/library/css/admin.css',
		array(), false, 'screen'
	);
}

/**
 * tarski_messages() - Adds messages about Tarski to the WordPress admin panel.
 * 
 * @since 2.1
 * @deprecated 2.4
 * @hook filter tarski_messages
 * Filter the messages Tarski prints to the WordPress admin panel.
 */
function tarski_messages() {
	_deprecated_function(__FUNCTION__, '2.4');
	
	$messages = apply_filters('tarski_messages', array());

	foreach ( $messages as $message ) {
		echo "<p class=\"tarski-message\">$message</p>\n\n";
	}
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
 * @deprecated 2.4
 * @return boolean
 */
function ready_to_delete_options($del_time) {
	_deprecated_function(__FUNCTION__, '2.4');
	
	if(!empty($del_time)) {
		$del_time = (int) $del_time;
		return (bool) (time() - $del_time) > (3 * 3600);
	}
}

/**
 * version_to_integer() - Turns Tarski version numbers into integers.
 * 
 * @since 2.0.3
 * @deprecated 2.3
 * @param string $version
 * @return integer
 */
function version_to_integer($version) {
	_deprecated_function(__FUNCTION__, '2.3');
	
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
 * @deprecated 2.3
 * @param mixed $version
 * @return boolean
 */
function version_newer_than($version) {
	_deprecated_function(__FUNCTION__, '2.3');
	
	$version = version_to_integer($version);
	$current = version_to_integer(theme_version('current'));

	if($version && $current) {
		return (bool) ($current > $version);
	}
}

?>