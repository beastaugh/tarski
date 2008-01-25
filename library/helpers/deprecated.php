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
 * add_tarski_option() - Adds a new Tarski option.
 * 
 * This function is an alias for update_tarski_option().
 * @deprecated 2.0.5
 * @since 1.6
 * @see update_tarski_option()
 * @param string $name
 * @param string $value
 * @return object $tarski_options
 */
function add_tarski_option($name, $value) {
	_deprecated_function(__FUNCTION__, '2.0.5', update_tarski_option($name, $value));
	
	update_tarski_option($name, $value);
}

/**
 * drop_tarski_option() - Drops the given Tarski option.
 * 
 * This function is just an alias for update_tarski_option(), but
 * with a more restricted set of parameters.
 * @deprecated 2.0.5
 * @since 1.6
 * @see update_tarski_option()
 * @param string $name
 * @return object $tarski_options
 */
function drop_tarski_option($option) {
	_deprecated_function(__FUNCTION__, '2.0.5', update_tarski_option($name, false));
	
	update_tarski_option($option, false);
}

/**
 * tarski_option() - Outputs the given Tarski option.
 * 
 * Basically just echoes the value returned by the complementary
 * function get_tarski_option().
 * @deprecated 2.0.5
 * @since 1.4
 * @see get_tarski_option()
 */
function tarski_option($name) {
	_deprecated_function(__FUNCTION__, '2.0.5', get_tarski_option($name));
	
	echo get_tarski_option($name);
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
 * tarski_header_status() - Outputs current header status.
 * 
 * Output is currently used to set an HTML class, which allows
 * the way the theme displays to be tweaked through CSS.
 * @deprecated 2.0
 * @since 1.2
 * @param boolean $return
 * @return string
 */
function tarski_header_status($return = false) {
	_deprecated_function(__FUNCTION__, '2.0');
	
	if(get_tarski_option('header') == 'blank.gif') {
		$status = 'noheaderimage';
	} else {
		$status = 'headerimage';
	}
	if($return) {
		return $status;
	} else {
		echo $status;
	}
}

/**
 * get_tarski_header_status() - Returns current header status.
 * 
 * Output is currently used to set an HTML class, which allows
 * the way the theme displays to be tweaked through CSS.
 * @deprecated 2.0
 * @since 1.7
 * @see tarski_header_status()
 * @return string
 */
function get_tarski_header_status() {
	_deprecated_function(__FUNCTION__, '2.0', tarski_header_status(true));
	
	tarski_header_status(true);
}

/**
 * get_tarski_navbar() - Returns the Tarski navbar.
 * 
 * @deprecated 2.0
 * @see tarski_navbar()
 * @return string
 */
function get_tarski_navbar() {
	_deprecated_function(__FUNCTION__, '2.0', tarski_navbar(true));
	
	tarski_navbar(true);
}

/**
 * tarski_navbar_feedlink() - Outputs feed link for the Tarski navbar.
 * 
 * @deprecated 2.0
 * @see tarski_feedlink()
 * @return string
 */
function tarski_navbar_feedlink($return = false) {
	_deprecated_function(__FUNCTION__, '2.0', tarski_feedlink());
	
	tarski_feedlink();
}

/**
 * get_tarski_bodyclass() - Outputs the classes that should be applied to the document body.
 * 
 * @deprecated 2.0
 * @see tarski_bodyclass()
 * @return string
 */
function get_tarski_bodyclass() {
	_deprecated_function(__FUNCTION__, '2.0', tarski_bodyclass(true));
	
	tarski_bodyclass(true);
}

/**
 * get_tarski_bodyid() - Returns the id that should be applied to the document body.
 * 
 * @deprecated 2.0
 * @see tarski_bodyid()
 * @return string
 */
function get_tarski_bodyid() {
	_deprecated_function(__FUNCTION__, '2.0', tarski_bodyid(true));
	
	tarski_bodyid(true);
}

/**
 * tarski_get_output() - Returns the output of functions that only echo.
 * 
 * This output-buffering function is a horrible hack and fortunately
 * can now (2.0) be deprecated.
 * @deprecated 2.0
 * @global object $comment
 * @global object $post
 */
function tarski_get_output($code) {
	_deprecated_function(__FUNCTION__, '2.0');
	
	global $comment, $post;
	ob_start();
	@eval($code);
	$return = ob_get_contents();
	ob_end_clean();
	return $return;
}

/**
 * link_pages_without_spaces() - Returns page links without the spaces WordPress seems to love.
 * 
 * @deprecated 2.0
 * @see tarski_link_pages()
 * @return string
 */
function link_pages_without_spaces($return = false) {
	_deprecated_function(__FUNCTION__, '2.0');
	
	if(!in_category(get_tarski_option('asidescategory'))) {
		tarski_get_output(link_pages('<p class="pagelinks"><strong>Pages</strong>', '</p>', 'number', '', '', '%', ''));
	
		$text = str_replace(' <a href', '<a href', $text);
		$text = str_replace('> ', '>', $text);
		apply_filters('link_pages_without_spaces', $text);
		if($return) {
			return $text;
		} else {
			echo $text;
		}
	}
}

/**
 * tarski_next_prev_pages() - Links to next and previous index pages.
 * 
 * @deprecated 2.0
 * @see
 * @return string
 */
function tarski_next_prev_pages() {
	_deprecated_function(__FUNCTION__, '2.0', tarski_posts_nav_link());
	
	tarski_posts_nav_link();
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
 * get_tarski_footer_blurb() - Outputs custom sidebar text.
 *
 * @deprecated 2.0
 * @see get_tarski_footer_blurb()
 * @return string
 */
function get_tarski_footer_blurb() {
	_deprecated_function(__FUNCTION__, '2.0', tarski_footer_blurb(true));
	
	tarski_footer_blurb(true);
}

/**
 * tarski_feed_and_credit() - Outputs feed link and Tarski credits.
 * 
 * @deprecated 2.0
 * @since 1.5
 * @see tarski_feedlink()
 * @see tarski_credits()
 */
function tarski_feed_and_credit() {
	_deprecated_function(__FUNCTION__, '2.0', tarski_feedlink() . tarski_credits());
	
	tarski_feedlink();
	tarski_credits();
}

/**
 * tarski_get_pages() - Retrieves a list of WordPress pages from the database.
 * 
 * Deprecated when it was realised that the get_pages() WP function was available.
 * @deprecated 2.0.4
 * @since 2.0.3
 * @see get_pages()
 * @global object $wpdb
 * @return array $pages
 */
function tarski_get_pages() {
	_deprecated_function(__FUNCTION__, '2.0.4');

	global $wpdb;
	$pages = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page' ORDER BY post_parent, menu_order");
	if(!empty($pages)) {
		return $pages;
	}
}

/**
 * tarski_resave_navbar() - Re-saves Tarski's navbar order whenever a page is edited.
 * 
 * This means that if the page order changes, the navbar order will change too.
 * @deprecated 2.0.5
 * @since 1.7
 * @see tarski_get_pages()
 */
function tarski_resave_navbar() {
	_deprecated_function(__FUNCTION__, '2.0.5');
	
	if(get_option('tarski_options')) {
		$pages = get_pages();
		$selected = explode(',', get_tarski_option('nav_pages'));
		
		if($pages && $selected) {
			$nav_pages = array();
			foreach($pages as $key => $page) {
				foreach($selected as $sel_page) {
					if($page->ID == $sel_page) {
						$nav_pages[$key] = $page->ID;
					}
				}
			}

			$condensed = implode(',', $nav_pages);
			update_tarski_option('nav_pages', $condensed);
		}
	}
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
			get_bloginfo('wpurl') . '/wp-admin/themes.php?page=tarski-options#footer_blurb',
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