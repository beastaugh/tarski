<?php

/**
 * tarski_sidebar() - Outputs Tarski's sidebar.
 * 
 * @since 2.0
 * @global object $post
 * @return mixed
 */
function tarski_sidebar() {
	global $post;
	
	$user_sidebar = TEMPLATEPATH . '/user-sidebar.php';
	
	if(file_exists($user_sidebar)) {
		include($user_sidebar);
	} elseif(is_archives_template()) {
		return;
	} else {
		echo "<div class=\"widgets\">\n";
		if(is_single() || is_page())
			dynamic_sidebar('sidebar-post-and-page');
		else
			dynamic_sidebar('sidebar-1');
		echo "</div>\n";
	}
}

/**
 * tarski_footer_main() - Outputs footer main widgets field.
 * 
 * @since 2.1
 * @return mixed
 */
function tarski_footer_main() {
	dynamic_sidebar('footer-main');
}

/**
 * tarski_footer_sidebar() - Outputs the footer sidebar widgets field.
 * 
 * @since 2.0
 * @return mixed
 */
function tarski_footer_sidebar() {
	dynamic_sidebar('sidebar-2');
}

/**
 * tarski_recent_entries() - Recent entries á la Tarski.
 *
 * Basically a ripoff of the WP widget function wp_widget_recent_entries().
 * @since 2.0.5
 * @see wp_widget_recent_entries()
 * @global object $posts
 * @return string
 */
function tarski_recent_entries() {
	global $posts;
	
	if ( $output = wp_cache_get('tarski_recent_entries') )
		return print($output);

	ob_start();
	if ( !$number = (int) $options['number'] )
		$number = 5;
	elseif ( $number < 1 )
		$number = 1;
	elseif ( $number > 10 )
		$number = 10;
	
	if ( is_home() )
		$offset = count($posts);
	else
		$offset = 0;

	$r = new WP_Query("showposts=$number&what_to_show=posts&nopaging=0&post_status=publish&offset=$offset");
	
	if ( $r->have_posts() )
		include(TARSKIDISPLAY . '/recent_articles.php');
	
	wp_cache_add('tarski_recent_entries', ob_get_flush());
}

/**
 * flush_tarski_recent_entries() - Deletes tarski_recent_entries() from the cache. 
 *
 * @since 2.0.5
 * @see tarski_recent_entries()
 */
function flush_tarski_recent_entries() {
	wp_cache_delete('tarski_recent_entries');
}

/**
 * tarski_widget_text_wrapper() - Wraps text widgets in content div with edit link.
 *
 * @since 2.1
 * @param string $text
 * @return string
 */
function tarski_widget_text_wrapper($text) {
	if ( is_user_logged_in() ) {
		$edit_link = sprintf(
			'<p class="edit-link">(<a title="%1$s" id="edit-footer-blurb" href="%2$s">%3$s</a>)</p>' . "\n",
			__('Change this text on the widgets page','tarski'),
			get_bloginfo('wpurl') . '/wp-admin/widgets.php',
			__('edit','tarski')
		);
	}
	
	if ( strlen(trim($text)) )
		$text = "<div class=\"content\">\n$text</div>\n$edit_link";
	
	return $text;
}

?>