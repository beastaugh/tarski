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
	
	if ( file_exists($user_sidebar) ) {
		include($user_sidebar);
	} elseif ( is_page_template('archives.php') ) {
		return;
	} else {
		if ( (is_single() || is_page()) && (get_tarski_option('sidebar_pp_type') != 'main') )
			dynamic_sidebar('sidebar-post-and-page');
		else
			dynamic_sidebar('sidebar-main');
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
	dynamic_sidebar('footer-sidebar');
}

/**
 * tarski_widget_text_wrapper() - Wraps text widgets in content div with edit link.
 *
 * @since 2.1
 * @param string $text
 * @return string
 */
function tarski_widget_text_wrapper($text) {
	if ( strlen(trim($text)) )
		$text = "<div class=\"content\">\n$text</div>\n";
	
	return $text;
}

/**
 * tarski_widget_links_args() - Removes navbar links from the links widget.
 * 
 * @since 2.2
 * @param array $args
 * @return array
 */
function tarski_widget_links_args($args) {
	$args['exclude_category'] = get_tarski_option('nav_extlinkcat');
	return $args;
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
function tarski_recent_entries($args) {	
	if ( $output = wp_cache_get('tarski_recent_entries') )
		return print($output);

	ob_start();
	extract($args);
	global $posts;
	// Allow for configuration in the future
	$options = array();
	// $options = get_option('tarski_recent_entries');
	$title = empty($options['title']) ? __('Recent Articles','tarski') : $options['title'];
	
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
?>
<div id="recent">
	<?php echo $before_title . $title . $after_title; ?>
	<ul>
		<?php while ($r->have_posts()) : $r->the_post(); ?>
		<li>
			<h4 class="recent-title"><a title="<?php _e('View this post', 'tarski'); ?>" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h4>
			<p class="recent-metadata"><?php
			echo the_time(get_option('date_format'));
			if(!get_tarski_option('hide_categories')) {
				_e(' in ', 'tarski'); the_category(', ');
			} ?></p>
			<div class="recent-excerpt content"><?php the_excerpt(); ?></div>
		</li>
		<?php endwhile; ?>
	</ul>
</div> <!-- /recent -->
<?php
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