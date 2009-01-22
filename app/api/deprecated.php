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