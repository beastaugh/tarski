<?php

/**
 * If the blog has more than one author, it prints a link to that author's archive page.
 */
function tarski_author_posts_link() {
	global $authordata;
	global $wpdb;
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
 * Returns the author object associated with an author archive page.
 */
function the_archive_author() {
	global $wp_query;
	$current_author = $wp_query->get_queried_object();
	return $current_author;
}

/**
 * Returns the display name of the author associated with a given archive page.
 */
function the_archive_author_displayname() {
	$current_author = the_archive_author();
	return $current_author->display_name;
}

/**
 * Returns the author description of the author associated with a given archive page.
 */
function the_archive_author_description() {
	$current_author = the_archive_author();
	return $current_author->user_description;
}

?>