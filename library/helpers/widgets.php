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
 * tarski_sidebar_links() - Returns an array for use with wp_list_bookmarks().
 * 
 * If a link category has been selected as external links in the navbar,
 * it will be excluded from this array.
 * @since 2.0
 * @return array $options
 */
function tarski_sidebar_links() {
	$link_cat_args = array(
		'orderby' => 'term_id',
		'exclude' => get_tarski_option('nav_extlinkcat'),
		'hierarchical'=> 0
	);
	
	$link_categories = &get_terms('link_category', $link_cat_args);
	
	foreach($link_categories as $link_cat)
		$link_cats[] = $link_cat->term_id;
	
	$link_cats = implode(',', $link_cats);
	
	$options = array(
		'category' => $link_cats,
		'category_before' => '',
		'category_after' => '',
		'title_before' => '<h3>',
		'title_after' => '</h3>',
		'show_images' => 0,
		'show_description' => 0,
	);
	
	$options = apply_filters('tarski_sidebar_links', $options);
	return $options;
}

/**
 * tarski_widget_links() - Tarski links widget.
 *
 * Doesn't display links from the category being used in the navbar,
 * if one is set.
 * @since 2.1
 * @see wp_widget_links()
 * @param array $args
 * @return string
 */
function tarski_widget_links($args) {
	extract($args, EXTR_SKIP);
	wp_list_bookmarks(array_merge(tarski_sidebar_links(), array('category_before' => $before_widget, 'category_after' => $after_widget)));
}

/**
 * tarski_widget_search() - Replaces the default search widget.
 *
 * Hopefully temporary, a patch has been proposed for WP 2.6
 * which does the same thing.
 * @since 2.1
 * @link http://trac.wordpress.org/ticket/5567
 */
function tarski_widget_search($args) {
	extract($args, EXTR_SKIP);
	$searchform_template = get_template_directory() . '/searchform.php';

	if ( !file_exists($searchform_template) )
		$searchform_template = get_theme_root() . '/default/searchform.php';

	echo $before_widget;
	include_once($searchform_template);
	echo $after_widget;
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
?>
<div id="recent">
	<h3><?php _e('Recent Articles','tarski'); ?></h3>
	<ul>
		<?php while ($r->have_posts()) : $r->the_post(); ?>
		<li>
			<h4 class="recent-title"><a title="<?php _e('View this post', 'tarski'); ?>" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h4>
			<p class="recent-metadata"><?php
			echo tarski_date();
			if(!get_tarski_option('hide_categories')) {
				_e(' in ', 'tarski'); the_category(', ');
			} ?></p>
			<div class="recent-excerpt content"><?php tarski_excerpt(); ?></div>
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