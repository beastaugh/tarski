<?php

/**
 * tarski_author_posts_link() - If the blog has more than one author, it outputs a link to that author's archive page.
 * 
 * @global object $authordata
 * @global object $wpdb
 * @return string
 */
function tarski_author_posts_link() {
	global $authordata, $wpdb;
	$count_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->usermeta WHERE `meta_key` = '" . $wpdb->prefix . "user_level' AND `meta_value` > 1");
	if($count_users > 1) {
		printf(
			__(' by ','tarski'). '<span class="vcard author"><a href="%1$s" title="%2$s" class="url fn">%3$s</a></span>', 
			get_author_posts_url($authordata->ID, $authordata->user_nicename), 
			sprintf(__('Articles by %s','tarski'), attribute_escape(get_the_author())), 
			get_the_author()
		);
	}
}

/**
 * the_archive_author() - Returns the author object associated with an author archive page.
 * 
 * @global object $wp_query
 * @return object $current_author
 */
function the_archive_author() {
	global $wp_query;
	$current_author = $wp_query->get_queried_object();
	return $current_author;
}

/**
 * the_archive_author_displayname() - Returns the display name of the author associated with a given archive page.
 * 
 * @return string
 */
function the_archive_author_displayname() {
	$current_author = the_archive_author();
	return $current_author->display_name;
}

/**
 * the_archive_author_description() - Returns the author description of the author associated with a given archive page.
 * 
 * @return string
 */
function the_archive_author_description() {
	$current_author = the_archive_author();
	return $current_author->user_description;
}

/**
 * get_author_feed_link() - Gets the feed link for a given author.
 * 
 * Can be set to return Atom, RSS or RSS2.
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

?>