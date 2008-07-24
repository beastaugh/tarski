<?php

/**
 * The scrapyard: deprecated functions that haven't yet been removed.
 * 
 * Don't write plugins that rely on these functions, as they are liable
 * to be removed between versions. There will usually be a better way
 * to do what you want; post on the forum if you need help.
 * @link http://tarskitheme.com/forum/
 */

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

/**
 * tarski_excerpt() - Excerpts a la Tarski.
 * 
 * Code shamelessly borrowed from Kaf Oseo's 'the_excerpt Reloaded' plugin.
 * @link http://guff.szub.net/2005/02/26/the-excerpt-reloaded/
 * @since 1.2.1
 * @deprecated 2.2
 * @param $return boolean
 * @param string $excerpt_length
 * @return string
 */
function tarski_excerpt($return = false, $excerpt_length = 35) {
	_deprecated_function(__FUNCTION__, '2.2', the_excerpt());
	
	global $post;

	if(!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) { // and it doesn't match cookie
			$output = get_the_password_form();
		}
		if($return) {
			return $output;
		} else {
			echo $output;
			return;
		}
	}

	if(!($text = $post->post_excerpt))
		$text = $post->post_content;

	if($excerpt_length < 0) {
		$output = $text;
	} else {
		str_replace('<!--more-->', '', $text);
		$text = explode(' ', $text);
		if(count($text) > $excerpt_length) {
			$l = $excerpt_length;
			$ellipsis = '&hellip;';
		} else {
			$l = count($text);
			$ellipsis = false;
		}
		for ($i = 0; $i < $l; $i++)
			$output .= $text[$i] . ' ';
	}

	$output = rtrim($output, " \n\t\r\0\x0B");
	$output = strip_tags($output);
	$output .= $ellipsis;
	$output = apply_filters('get_the_excerpt', $output);
	$output = apply_filters('the_excerpt', $output);
	$output = apply_filters('tarski_excerpt', $output);

	if($return)
		return $output;
	else
		echo $output;
}

/**
 * tarski_date() - Tweaked WordPress date function that shows up on every post.
 * 
 * The WP function the_date only shows up on the first post
 * of that day. This one displays on every post, regardless
 * of how many posts are made that day.
 * @since 1.2.2
 * @deprecated 2.2
 * @see the_time()
 * @global object $post
 * @return string
 * @hook filter tarski_date
 * Filter for the date formatting that Tarski uses to ensure that dates are
 * displayed everywhere using the user's date preferences.
 */
function tarski_date() {
	_deprecated_function(__FUNCTION__, '2.2', get_the_time(get_option('date_format')));
	
	global $post;
	$date = mysql2date(get_option('date_format'), $post->post_date);
	return apply_filters('tarski_date', $date);
}

/**
 * add_robots_meta() - Adds robots meta element if blog is public.
 * 
 * WordPress adds a meta element denying robots access if the site is set
 * to private, but it doesn't add one allowing them if it's set to public.
 * @since 2.0
 * @deprecated 2.1
 * @see Asset::meta()
 * @return string
 */
function add_robots_meta() {
	_deprecated_function(__FUNCTION__, '2.1', Asset::meta());
	
	if(get_option('blog_public') != '0')
		echo '<meta name="robots" content="all" />' . "\n";
}

/**
 * tarski_stylesheets() - Adds Tarski's stylesheets to the document head.
 * 
 * @deprecated 2.1
 * @since 2.0.1
 * @return string
 */
function tarski_stylesheets() {
	_deprecated_function(__FUNCTION__, '2.1', Asset::stylesheets());
	
	$assets = new Asset;
	$assets->stylesheets();
	$assets->output();
}

/**
 * tarski_feeds() - Outputs feed links for the page.
 * 
 * Can be set to return Atom, RSS or RSS2. Will always return the
 * main site feed, but will additionally return an archive, search
 * or comments feed depending on the page type.
 * @deprecated 2.1
 * @since 2.0
 * @see Asset::feeds()
 * @return string
 */
function tarski_feeds() {
	_deprecated_function(__FUNCTION__, '2.1', Asset::feeds());
	
	$assets = new Asset;
	$assets->feeds();
	$assets->output();
}

/**
 * tarski_javascript() - Adds Tarski JavaScript to the document head.
 * 
 * @deprecated 2.1
 * @since 2.0.1
 * @return string
 */
function tarski_javascript() {
	_deprecated_function(__FUNCTION__, '2.1', Asset::javascript());
	
	$assets = new Asset;
	$assets->javascript();
	$assets->output();
}

/**
 * hide_sidebar_for_archives() - Hides the sidebar on pages using the Archives template.
 * 
 * @deprecated 2.1
 * @since 2.0
 * @return string|boolean $sidebar_file
 */
function hide_sidebar_for_archives($sidebar_file) {
	_deprecated_function(__FUNCTION__, '2.1');
	
	if(is_archives_template())
		$sidebar_file = false;
	return $sidebar_file;
}

/**
 * is_active_sidebar() - Checks to see whether a particular sidebar has widgets.
 * 
 * Stolen from ticket #4594 on Trac, hence the conditional definition. No longer
 * needed in 2.1 since all sidebars are widgets and only widgets, so default
 * states are not needed.
 * @link http://trac.wordpress.org/ticket/4594
 * @deprecated 2.1
 * @since 2.0
 * @return boolean
 */
if(!function_exists('is_active_sidebar')) {
	function is_active_sidebar( $index ) {
		_deprecated_function(__FUNCTION__, '2.1');
		
		$index = ( is_int($index) ) ? "sidebar-$index" : sanitize_title($index);
		$sidebars_widgets = (array) get_option('sidebars_widgets');	
		return (bool) ( isset( $sidebars_widgets[$index] ) );
	}
}

/**
 * tarski_sidebar_custom() - Returns custom sidebar content, appropriately formatted.
 *
 * Gets the database value; strips slashes; prettifies the quotes
 * and other typographical nuances; converts ampersands and other
 * characters in need of encoding as HTML entities; applies
 * automatic paragaphing; and finally applies filters and returns
 * the output.
 * @deprecated 2.1
 * @since 2.0
 * @return string
 */
function tarski_sidebar_custom($return = false) {
	_deprecated_function(__FUNCTION__, '2.1');
	
	$content = get_tarski_option('sidebar_custom');
	$output = apply_filters('tarski_sidebar_custom', $content);
	if($return) {
		return $output;
	} else {
		echo $output;
	}
}

/**
 * tarski_footer_blurb() - Outputs custom footer content, appropriately formatted.
 *
 * Gets the database value; strips slashes; prettifies the quotes
 * and other typographical nuances; converts ampersands and other
 * characters in need of encoding as HTML entities; applies
 * automatic paragaphing; and finally applies filters and returns
 * the output.
 * @deprecated 2.1
 * @since 2.0
 * @param boolean $return
 * @return string
 */
function tarski_footer_blurb($return = false) {
	_deprecated_function(__FUNCTION__, '2.1');
	
	$content = get_tarski_option('blurb');	
	$output = apply_filters('tarski_footer_blurb', $content);
	
	if($return) {
		return $output;
	} else {
		echo $output;
	}
}

/**
 * tarski_blurb_wrapper() - Wraps footer blurb in div element.
 *
 * @deprecated 2.1
 * @since 2.0
 * @see tarski_footer_blurb()
 * @param string $blurb
 * @return string
 */
function tarski_blurb_wrapper($blurb) {
	_deprecated_function(__FUNCTION__, '2.1');
	
	if(is_user_logged_in()) {
		$edit_link = sprintf(
			'<p class="edit-link">(<a title="%1$s" id="edit-footer-blurb" href="%2$s">%3$s</a>)</p>' . "\n",
			__('Edit the footer content area'),
			admin_url('themes.php?page=tarski-options#footer_blurb'),
			__('edit','tarski')
		);
	}
	
	if(get_tarski_option('blurb')) {
		$blurb = "<div class=\"content\">\n$blurb</div>\n$edit_link";
		$blurb = "<div id=\"blurb\">\n$blurb</div> <!-- /blurb -->\n";
	}
	
	return $blurb;
}

/**
 * is_archives_template() - Returns true if the current page uses the Archives template
 * 
 * This function exists for backwards-compatibility with WordPress 2.3,
 * which does not include the is_page_template() function.
 * @deprecated 2.1
 * @since 2.0
 * @global object $post
 * @return boolean
 */
function is_archives_template() {
	_deprecated_function(__FUNCTION__, '2.1', is_page_template('archives.php'));
	
	return is_page_template('archives.php');
}

?>