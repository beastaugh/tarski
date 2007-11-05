<?php

/**
 * is_archives_template() - Returns true if the current page uses the Archives template
 * 
 * This function exists for backwards-compatibility with WordPress 2.3,
 * which does not include the is_page_template() function.
 * @since 2.0
 * @global object $post
 * @return boolean
 */
function is_archives_template() {
	if(function_exists('is_page_template')) {
		return is_page_template('archives.php');
	} else {
		global $post;
		$template = get_post_meta($post->ID, '_wp_page_template', true);
	
		return ($template == 'archives.php');
	}
}

/**
 * hide_sidebar_for_archives() - Hides the sidebar on pages using the Archives template
 * 
 * @since 2.0
 * @global object $post
 * @return string $sidebar_file
 */
function hide_sidebar_for_archives($sidebar_file) {
	global $post;
	
	if(is_archives_template()) {
		$sidebar_file = false;
	}
	
	return $sidebar_file;
}

?>