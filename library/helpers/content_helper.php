<?php

/**
 * is_active_sidebar() - Checks to see whether a particular sidebar has widgets.
 * 
 * Stolen from ticket #4594 on Trac, hence the conditional definition.
 * @link http://trac.wordpress.org/ticket/4594
 * @since 2.0
 * @return boolean
 */
if(!function_exists('is_active_sidebar')) {
	function is_active_sidebar( $index ) {
		$index = ( is_int($index) ) ? "sidebar-$index" : sanitize_title($index);
		$sidebars_widgets = (array) get_option('sidebars_widgets');	
		return (bool) ( isset( $sidebars_widgets[$index] ) );
	}
}

/**
 * tarski_sidebar() - Outputs Tarski's sidebar.
 * 
 * @since 2.0
 * @global object $post
 * @return mixed
 */
function tarski_sidebar() {
	global $post;

	// Default Tarski sidebar
	$sidebar_file = TARSKIDISPLAY . '/sidebar/tarski_sidebar.php';
	
	// Normal sidebar
	if(get_tarski_option('sidebar_type') == 'widgets') {
		$sidebar_file = TARSKIDISPLAY . '/sidebar/widgets_sidebar.php';
	} elseif(get_tarski_option('sidebar_type') == 'custom') {
		if(file_exists(TEMPLATEPATH . '/user-sidebar.php')) {
			$sidebar_file = TEMPLATEPATH . '/user-sidebar.php';
		} elseif(is_user_logged_in()) {
			$sidebar_file = TARSKIDISPLAY . '/sidebar/user_sidebar_error.php';
		}
	}
	
	// Single post and page sidebar
	if(is_single() || is_page()) {
		if(get_tarski_option('sidebar_pp_type') == 'widgets') {
			$sidebar_file = TARSKIDISPLAY . '/sidebar/widgets_pp_sidebar.php';
		} elseif(get_tarski_option('sidebar_pp_type') == 'none') {
			return;
		}
	}
	
	$sidebar_file = apply_filters('tarski_sidebar', $sidebar_file);
	
	if(is_string($sidebar_file))
		include($sidebar_file);
}

/**
 * hide_sidebar_for_archives() - Hides the sidebar on pages using the Archives template
 * 
 * @since 2.0
 * @return string|boolean $sidebar_file
 */
function hide_sidebar_for_archives($sidebar_file) {	
	if(is_archives_template())
		$sidebar_file = false;
	return $sidebar_file;
}

/**
 * tarski_next_prev_posts() - Outputs links to the next and previous posts.
 * 
 * WordPress has this functionality, but the built-in formatting isn't
 * to Tarski's tastes, so this function builds its own.
 * @since 1.2
 * @return string
 */
function tarski_next_prev_posts() {
	$prev_post = get_previous_post();
	$next_post = get_next_post();
	if($prev_post || $next_post) {
		echo '<p class="primary-span articlenav">';

		if($prev_post) {
			echo '<span class="previous-entry">';
			previous_post_link('%link','&lsaquo; %title');
			echo '</span>';

			if($next_post) {
				echo ' <span class="separator">&nbsp;&bull;&nbsp;</span> ';
			}
		}

		if($next_post) {
			echo '<span class="next-entry">';
			next_post_link('%link','%title &rsaquo;');
			echo '</span>';
		}

		echo "</p>\n";
	}
}

/**
 * tarski_link_pages() - Tarski wrapper around wp_link_pages().
 * 
 * @since 2.0
 * @return string
 */
function tarski_link_pages() {
	$arguments = array(
		'before' => '<p class="link-pages"><strong>' . __('Pages:','tarski') . '</strong>',
		'after' => '</p>',
		'next_or_number' => 'number',
		'nextpagelink' => __('Next page','tarski'),
		'previouspagelink' => __('Previous page','tarski'),
		'pagelink' => '%',
		'more_file' => '',
		'echo' => 1
	);
	
	if(!in_category(get_tarski_option('asidescategory'))) {
		wp_link_pages($arguments);
	}
}

/**
 * tarski_posts_nav_link() - Outputs next / previous index page links.
 * 
 * @since 1.2
 * @global object $wp_query
 * @return string
 */
function tarski_posts_nav_link() {
	if(get_tarski_option('use_pages')) {
		global $wp_query;
				
		if(!is_singular()) {
			$max_num_pages = $wp_query->max_num_pages;
			$paged = get_query_var('paged');
			$sep = ' &sect; ';
			
			// Only have sep if there's both prev and next results
			if ($paged < 2 || $paged >= $max_num_pages) {
				$sep = '';
			}
		
			if($max_num_pages > 1) {
				echo '<p class="pagination">';
				if(is_search()) {
					previous_posts_link('&laquo; ' . __('Previous results','tarski'));
					echo $sep;
					next_posts_link(__('More results','tarski') . ' &raquo;');
				} else {
					next_posts_link('&laquo; ' . __('Older entries','tarski'));
					echo $sep;
					previous_posts_link(__('Newer entries','tarski') . ' &raquo;');
				}
				echo "</p>\n";				
			}
		}
	}
}

/**
 * tarski_date() - Tweaked WordPress date function that shows up on every post.
 * 
 * The WP function the_date only shows up on the first post
 * of that day. This one displays on every post, regardless
 * of how many posts are made that day.
 * @since 1.2.2
 * @global object $post
 * @return string
 */
function tarski_date() {
	global $post;
	return mysql2date(get_option('date_format'), $post->post_date);
}

/**
 * tarski_post_categories_link() - Outputs post categories
 * 
 * Categories list is nicely wrapped for potential DOM interactions
 * via JavaScript, CSS etc.
 * @since 2.0
 * @return string
 */
function tarski_post_categories_link() {
	if(get_tarski_option('show_categories')) {
		printf(
			__(' in %s','tarski'),
			'<span class="categories">' . get_the_category_list(', ') . '</span>'
		);
	}
}

/**
 * tarski_comments_link() - Outputs comments links.
 *
 * @since 2.1
 * @global object $post
 * @return string
 */
function tarski_comments_link() {
	global $post;
	if($post->comment_status == 'open' || $post->comment_count > 0) {
		if(is_single() || is_page()) {
			echo ' | <a class="comments-link" href="#comments">'; comments_number(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski')); echo '</a>';
		} else {
			echo ' | ';
			comments_popup_link(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski'), 'comments-link', __('Comments closed', 'tarski'));
		}
	}
}

/**
 * tarski_asides_permalink() - Outputs permalink text for asides.
 *
 * @since 2.1
 * @global object $post
 * @return string
 */
function tarski_asides_permalink_text() {
	global $post;
	if($post->comment_status == 'open' || $post->comment_count > 0) {
		comments_number(__('No comments','tarski'), __('1 comment','tarski'), '%' . __(' comments','tarski'));
	} else {
		_e('Permalink', 'tarski');
	}
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
	
	$link_cats = array();
	
	foreach($link_categories as $link_cat)
		array_push($link_cats, $link_cat->term_id);
	
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
 * tarski_comment_datetime() - Ties the date and time together.
 * 
 * Makes the comment date and time output more translateable.
 * @since 2.0
 * @return string
 */
function tarski_comment_datetime() {
	$datetime = sprintf(
		__('%1$s at %2$s','tarski'),
		get_comment_date(),
		get_comment_time()
	);
	$datetime = apply_filters('tarski_comment_datetime', $datetime);
	echo $datetime;
}

/**
 * tidy_openid_names() - Strips the http:// prefix from OpenID names.
 * 
 * @since 2.0
 * @global object $comment_author
 * @return string $comment_author
 */
function tidy_openid_names($comment_author) {
	global $comment;
	$comment_author =  str_replace('http://', '', $comment_author);
	$comment_author = rtrim($comment_author, '/');
	return $comment_author;
}

/**
 * tarski_comment_author_link() - Returns a comment author's name, wrapped in a link if present.
 * 
 * It also includes hCard microformat markup.
 * @link http://microformats.org/wiki/hcard
 * @since 2.0
 * @global object $comment
 * @return string
 */
function tarski_comment_author_link() {
	global $comment;
	$url = get_comment_author_url();
	$author = get_comment_author();

	if(empty($url) || 'http://' == $url) {
		$return = sprintf(
			'<span class="fn">%s</span>',
			$author
		);
	} else {
		$return = sprintf(
			'<a class="url fn" href="%1$s" rel="external nofollow">%2$s</a>',
			$url,
			$author
		);
	}

	$return =  apply_filters('get_comment_author_link', $return);
	$return = apply_filters('tarski_comment_author_link', $return);
	return $return;
}

/**
 * tarski_excerpt() - Excerpts a la Tarski.
 * 
 * Code shamelessly borrowed from Kaf Oseo's 'the_excerpt Reloaded' plugin.
 * @link http://guff.szub.net/2005/02/26/the-excerpt-reloaded/
 * @since 1.2.1
 * @param $return boolean
 * @param string $excerpt_length
 * @return string
 */
function tarski_excerpt($return = false, $excerpt_length = 35) {
	global $post;

	if(!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) { // and it doesn't match cookie
			$output = get_the_password_form();
		}
		if($return) {
			return $output;
		} else {
			echo $output;
			return;
		}
	}
	
	if(!($text = $post->post_excerpt))
		$text = $post->post_content;

	if($excerpt_length < 0) {
		$output = $text;
	} else {
		str_replace('<!--more-->', '', $text);
		$text = explode(' ', $text);
		if(count($text) > $excerpt_length) {
			$l = $excerpt_length;
			$ellipsis = '&hellip;';
		} else {
			$l = count($text);
			$ellipsis = false;
		}
		for ($i = 0; $i < $l; $i++)
			$output .= $text[$i] . ' ';
	}

	$output = rtrim($output, " \s\n\t\r\0\x0B");
	$output = strip_tags($output);
	$output .= $ellipsis;
	$output = apply_filters('get_the_excerpt', $output);
	$output = apply_filters('the_excerpt', $output);
	$output = apply_filters('tarski_excerpt', $output);
	
	if($return)
		return $output;
	else
		echo $output;
}

/**
 * tarski_404_content() - Outputs default text for 404 error pages.
 *
 * @since 1.5
 * @return string
 */
function tarski_404_content() {
	$content = sprintf(
		__('The page you are looking for does not exist; it may have been moved, or removed altogether. You might want to try the search function or return to the %s.','tarski'),
		'<a href="' . user_trailingslashit(get_bloginfo('url')) . '">' . __('front page','tarski') . '</a>'
	);
	$content = wpautop($content);
	$content = apply_filters('th_404_content', $content);
	echo $content;
}

/**
 * tarski_content_massage() - Filter adding smart quotes, auto-paragraphs etc.
 * 
 * This function strips slashes, adds smart quotes and other typographical
 * niceties, converts characters such as ampersands to their HTML equivalent,
 * adds automatic paragraphing and line breaks, and finally returns the
 * altered content.
 * @since 2.0.5
 * @param string $input
 * @return string $output
 *
 */
function tarski_content_massage($input) {
	if(!empty($input)) {
		$output = wpautop(convert_chars(wptexturize(stripslashes($input))));
	}
	return $output;
}

/**
 * tarski_sidebar_custom() - Returns custom sidebar content, appropriately formatted.
 *
 * Gets the database value; strips slashes; prettifies the quotes
 * and other typographical nuances; converts ampersands and other
 * characters in need of encoding as HTML entities; applies
 * automatic paragaphing; and finally applies filters and returns
 * the output.
 * @since 2.0
 * @return string
 */
function tarski_sidebar_custom($return = false) {
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
 * @since 2.0
 * @param boolean $return
 * @return string
 */
function tarski_footer_blurb($return = false) {
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
 * @since 2.0
 * @see tarski_footer_blurb()
 * @param string $blurb
 * @return string
 */
function tarski_blurb_wrapper($blurb) {	
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
 * tarski_recent_entries() - Recent entries á la Tarski.
 *
 * Basically a ripoff of the WP widget function wp_widget_recent_entries().
 * @since 2.0.5
 * @see wp_widget_recent_entries()
 * @global object $posts
 * @param integer $number_of_entries
 * @return string
 */
function tarski_recent_entries($number_of_entries = 5) {
	global $posts;
	
	if ( !get_tarski_option('footer_recent') )
		return false;
	
	if ( $output = wp_cache_get('tarski_recent_entries') )
		return print($output);

	ob_start();
	if ( !$number = (int) $number_of_entries )
		$number = 5;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 10 )
		$number = 10;
	
	if ( is_home() )
		$offset = count($posts);
	else
		$offset = 0;

	$r = new WP_Query("showposts=$number&what_to_show=posts&nopaging=0&post_status=publish&offset=$offset");
	
	if ($r->have_posts()) {
		include(TARSKIDISPLAY . '/recent_articles.php');
	}
	
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
 * tarski_footer_sidebar() - Outputs the footer sidebar.
 * 
 * Will output widgets if any have been added to that sidebar,
 * otherwise it adds the default search form.
 * @since 2.0
 * @return mixed
 */
function tarski_footer_sidebar() {
	if(is_active_sidebar('sidebar-2')) {
		include(TARSKIDISPLAY . '/sidebar/widgets_footer_sidebar.php');
	} else {
		tarski_searchform();
	}
}

?>