<?php

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
	$options['number'] = 5;
	
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
	
	if ( $r->have_posts() ) {
		include(TARSKIWIDGETS . '/recent_articles_display.php');
		unset($r);
		wp_reset_query();  // Restore global post data stomped by the_post().
	}
	
	wp_cache_add('tarski_recent_entries', ob_get_flush(), 'widget');
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

?>