<?php // WordPress' own author functions don't work for author archive pages, so this file provides a few replacements.

function tarski_author_posts_link() {
	global $authordata;
	printf( 
		'<a href="%1$s" title="%2$s" class="author">%3$s</a>', 
		get_author_posts_url($authordata->ID, $authordata->user_nicename), 
		sprintf(__('Articles by %s','tarski'), attribute_escape(get_the_author())), 
		get_the_author()
	);
}

function the_archive_author() {
	global $wp_query;
	$current_author = $wp_query->get_queried_object();
	return $current_author;
}

function the_archive_author_displayname() {
	$current_author = the_archive_author();
	return $current_author->display_name;
}

function the_archive_author_description() {
	$current_author = the_archive_author();
	return $current_author->user_description;
}

?>