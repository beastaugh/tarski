<?php

/**
 * is_page_template() - Determine whether or not we are in a page template
 *
 * This template tag allows you to determine whether or not you are in a page template.
 * You can optionally provide a template name and then the check will be specific to
 * that template.
 *
 * @global object $wp_query
 * @param string $template The specific template name if specific matching is required
 */
if(!function_exists('is_page_template')) {
	function is_page_template($template = '') {
		if (!is_page()) {
			return false;
		}

		global $wp_query;

		$page = $wp_query->get_queried_object();
		$custom_fields = get_post_custom_values('_wp_page_template',$page->ID);
		$page_template = $custom_fields[0];

		// We have no argument passed so just see if a page_template has been specified
		if ( empty( $template ) ) {
			if (!empty( $page_template ) ) {
				return true;
			}
		} elseif ( $template == $page_template) {
			return true;
		}

		return false;
	}
}

/**
 * get_category_feed_link() - Gets the feed link for a given category.
 * 
 * Can be set to return Atom, RSS or RSS2. Now in WP trunk, but
 * conditionally defined here for backwards compatibility with 2.3.
 * @link http://trac.wordpress.org/changeset/6327
 * @since 2.0
 * @param integer $cat_id
 * @param string $feed
 * @return string $link
 */
if(!function_exists('get_category_feed_link')) {
	function get_category_feed_link($cat_id, $feed = 'rss2') {
		$cat_id = (int) $cat_id;

		$category = get_category($cat_id);

		if ( empty($category) || is_wp_error($category) )
			return false;

		$permalink_structure = get_option('permalink_structure');

		if ( '' == $permalink_structure ) {
			$link = get_option('home') . "?feed=$feed&amp;cat=" . $cat_id;
		} else {
			$link = get_category_link($cat_id);
			if( 'rss2' == $feed )
				$feed_link = 'feed';
			else
				$feed_link = "feed/$feed";

			$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
		}

		$link = apply_filters('category_feed_link', $link, $feed);

		return $link;
	}
}

/**
 * get_author_feed_link() - Gets the feed link for a given author.
 * 
 * Can be set to return Atom, RSS or RSS2. Conditionally defined
 * since this function is now in WordPress 2.4.
 * @since 2.0
 * @param integer $author_id
 * @param string $feed
 * @return string $link
 */
if(!function_exists('get_author_feed_link')) {
	function get_author_feed_link($author_id, $feed = 'rss2') {
		$auth_id = (int) $author_id;

		$author = get_userdata($auth_id);
		
		if ( empty($author) || is_wp_error($author) )
			return false;

		$permalink_structure = get_option('permalink_structure');

		if ( '' == $permalink_structure ) {
			$link = get_option('home') . "?feed=$feed&amp;author=" . $auth_id;
		} else {
			$link = get_author_posts_url($auth_id);
			if ( 'rss2' == $feed )
				$feed_link = 'feed';
			else
				$feed_link = "feed/$feed";

			$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
		}

		$link = apply_filters('author_feed_link', $link, $feed);

		return $link;
	}
}


/**
 * get_search_feed_link() - Returns the feed link for a given search query
 *
 * Conditionally defined since it is now included in WordPress 2.4.
 * @link http://trac.wordpress.org/changeset/6413
 * @param string $search_query
 * @param string $feed
 * @return string
 */
if(!function_exists('get_search_feed_link')) {
	function get_search_feed_link($search_query = '', $feed = '') {
		if ( empty($search_query) )
			$search = attribute_escape(get_search_query());
		else
			$search = attribute_escape(stripslashes($search_query));
		
		if ( empty($feed) )
			$feed = get_default_feed();
		
		$link = get_option('home') . "?s=$search&amp;feed=$feed";
		
		$link = apply_filters('search_feed_link', $link);
		
		return $link;
	}
}

/**
 * get_search_comments_feed_link() - Returns the feed link for the comments on posts matching a given search query
 *
 * Conditionally defined since it is now included in WordPress 2.4.
 * @link http://trac.wordpress.org/changeset/6413
 * @since 2.1
 * @param string $search_query
 * @param string $feed
 * @return string
 */
if(!function_exists('get_search_comments_feed_link')) {
	function get_search_comments_feed_link($search_query = '', $feed = '') {
		if ( empty($search_query) )
			$search = attribute_escape(get_search_query());
		else
			$search = attribute_escape(stripslashes($search_query));
		
		if ( empty($feed) )
			$feed = get_default_feed();
		
		$link = get_option('home') . "?s=$search&amp;feed=comments-$feed";
		
		$link = apply_filters('search_feed_link', $link);
		
		return $link;
	}
}

?>