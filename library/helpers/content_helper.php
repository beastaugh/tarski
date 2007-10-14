<?php

/**
 * tarski_next_prev_posts() - Outputs links to the next and previous posts.
 * 
 * WordPress has this functionality, but the built-in
 * formatting isn't to Tarski's tastes, so this function
 * builds its own.
 * @return string
 */
function tarski_next_prev_posts() {
	if(get_previous_post() || get_next_post()) {
		$prefix = '<p class="primary-span articlenav">';
		$suffix = "</p>\n";
	}
	if(get_previous_post() && get_next_post()) {
		$separator = ' &nbsp;&bull;&nbsp; ';
	}
	
	echo $prefix;
	previous_post_link('%link','&lsaquo; %title');
	echo $separator;
	next_post_link('%link','%title &rsaquo;');
	echo $suffix;
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
 * @global object $wp_query
 * @return string
 */
function tarski_posts_nav_link() {
	global $wp_query;
	$wp_query->is_paged = true;
	
	if(is_paged() && get_tarski_option('use_pages')) {
		if(is_search()) {
			$prev_text = __('Previous results','tarski');
			$next_text = __('More results','tarski');
		} else {
			$prev_text = __('Newer entries','tarski');
			$next_text = __('Older entries','tarski');
		}

		echo '<p class="pagination">';
		posts_nav_link(" &sect; ", "&laquo; $prev_text", "$next_text &raquo;");
		echo "</p>\n";
	}
}

/**
 * tarski_date() - Tweaked WordPress date function that shows up on every post.
 * 
 * The WP function the_date only shows up on the first post
 * of that day. This one displays on every post, regardless
 * of how many posts are made that day.
 * @global object $post
 * @return string
 */
function tarski_date() {
	global $post;
	return mysql2date(get_option('date_format'), $post->post_date);
}

function tarski_post_categories_link() {
	if(get_tarski_option('show_categories')) {
		printf(
			__(' in %s','tarski'),
			'<span class="categories">' . get_the_category_list(', ') . '</span>'
		);
	}
}

/**
 * tarski_sidebar_links() - Returns an array for use with wp_list_bookmarks().
 * 
 * @return array $options
 */
function tarski_sidebar_links() {
	$options = array(
		'category_before' => '',
		'category_after' => '',
		'title_before' => '<h3>',
		'title_after' => '</h3>',
		'show_images' => 0,
		'show_description' => 0
	);
	$options = apply_filters('tarski_sidebar_links', $options);
	return $options;
}

/**
 * add_post_tags() - Appends tags to posts.
 * 
 * @return string
 */
function add_post_tags() {
	if(function_exists('the_tags')) {
		if(is_single() || (get_tarski_option('tags_everywhere')) && !in_category(get_tarski_option('asidescategory'))) {
			the_tags('<p class="tagdata"><strong>'. __('Tags','tarski'). ':</strong> ', ', ', '</p>'."\n");
		}
	}
}

/**
 * tidy_openid_names() - Strips the http:// prefix from OpenID names.
 * 
 * @global object $comment_author
 * @return string $comment_author
 */
function tidy_openid_names($comment_author) {
	global $comment;
	$comment_author =  str_replace('http://', '', $comment_author);
	$comment_author = rtrim($comment_author, "/");
	return $comment_author;
}

/**
 * tarski_comment_author_link() - Returns a comment author's name, wrapped in a link if present.
 * 
 * It also includes hCard microformat markup.
 * @link http://microformats.org/wiki/hcard
 * @global object $comment
 * @return string
 */
function tarski_comment_author_link() {
	global $comment;
	$url = get_comment_author_url();
	$author = get_comment_author();

	if(empty($url) || 'http://' == $url) {
		$return = '<span class="fn">'. $author. '</span>';
	} else {
		$return = '<a class="url fn" href="'. $url. '" rel="external nofollow">'. $author. '</a>';
	}
	
	return apply_filters('get_comment_author_link', $return);
}

/**
 * tarski_excerpt() - Tarski excerpts, improving on the core WordPress code.
 * 
 * Code shamelessly borrowed from Kaf Oseo's 'the_excerpt Reloaded' plugin.
 * @link http://guff.szub.net/2005/02/26/the-excerpt-reloaded/
 * @param string $excerpt_length
 * @param string $allowedtags
 * @param string $filter_type
 * @param integer $use_more_link
 * @param string $more_link_text
 * @param integer $force_more
 * @param integer $fakeit
 * @param integer $no_more
 * @param string $more_tag
 * @param string $more_link_title
 * @param integer $showdots
 * @param string $allowedtags
 * @global object $post
 * @return string
 */
function tarski_excerpt($excerpt_length = 120, $allowedtags = '', $filter_type = 'none', $use_more_link = 1, $more_link_text = '(more...)', $force_more = 1, $fakeit = 1, $no_more = 0, $more_tag = 'div', $more_link_title = 'Continue reading this entry', $showdots = 1) {
	global $post;

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) { // and it doesn't match cookie
			if(is_feed()) { // if this runs in a feed
				$output = __('This entry is protected.','tarski');
			} else {
				$output = get_the_password_form();
			}
		}
		return $output;
	}

	if($fakeit == 2) { // force content as excerpt
		$text = $post->post_content;
	} elseif($fakeit == 1) { // content as excerpt, if no excerpt
		$text = (empty($post->post_excerpt)) ? $post->post_content : $post->post_excerpt;
	} else { // excerpt no matter what
		$text = $post->post_excerpt;
	}

	if($excerpt_length < 0) {
		$output = $text;
	} else {
	if(!$no_more && strpos($text, '<!--more-->')) {
		$text = explode('<!--more-->', $text, 2);
			$l = count($text[0]);
			$more_link = 1;
		} else {
			$text = explode(' ', $text);
			if(count($text) > $excerpt_length) {
				$l = $excerpt_length;
				$ellipsis = 1;
			} else {
				$l = count($text);
				$more_link_text = '';
				$ellipsis = 0;
			}
		}
		for ($i=0; $i<$l; $i++)
			$output .= $text[$i] . ' ';
	}

	if('all' != $allowed_tags) {
		$output = strip_tags($output, $allowedtags);
	}

	$output = rtrim($output, "\s\n\t\r\0\x0B");
	$output = ($fix_tags) ? $output : balanceTags($output);
	$output .= ($showdots && $ellipsis) ? '...' : '';

	switch($more_tag) {
		case('div') :
			$tag = 'div';
			break;
		case('span') :
			$tag = 'span';
			break;
		case('p') :
			$tag = 'p';
			break;
		default :
			$tag = 'span';
			break;
	}

	if ($use_more_link && $more_link_text) {
		if($force_more) {
			$output .= ' <' . $tag . ' class="more-link"><a href="'. get_permalink($post->ID) . '#more-' . $post->ID .'" title="' . $more_link_title . '">' . $more_link_text . '</a></' . $tag . '>' . "\n";
		} else {
			$output .= ' <' . $tag . ' class="more-link"><a href="'. get_permalink($post->ID) . '" title="' . $more_link_title . '">' . $more_link_text . '</a></' . $tag . '>' . "\n";
		}
	}

	$output = apply_filters($filter_type, $output);
	return $output;
}

/**
 * tarski_404_content() - Outputs default text for 404 error pages.
 *
 * @return string
 */
function tarski_404_content() {
	$content = sprintf(
		__('The page you are looking for does not exist; it may have been moved, or removed altogether. You might want to try the search function or return to the %s.','tarski'),
		'<a href="'. user_trailingslashit(get_bloginfo('url')). '">'. __('front page','tarski'). '</a>'
	);
	$content = wpautop($content);
	$content = apply_filters('th_404_content', $content);
	echo $content;
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
	$output = wpautop(convert_chars(wptexturize(stripslashes(get_tarski_option('sidebar_custom')))));
	$output = apply_filters('tarski_sidebar_custom', $output);
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
 * @param boolean $return
 * @return string
 */
function tarski_footer_blurb($return = false) {
	$output = wpautop(convert_chars(wptexturize(stripslashes(get_tarski_option('blurb')))));
	$output = apply_filters('tarski_footer_blurb', $output);
	if($return) {
		return $output;
	} else {
		echo $output;
	}
}

/**
 * tarski_blurb_wrapper() - Wraps footer blurb in div element.
 *
 * @param string $blurb
 * @return string
 */
function tarski_blurb_wrapper($blurb) {
	$prefix = '<div class="content">'."\n";
	$suffix = '</div> <!-- /blurb -->'."\n";
	
	if(get_tarski_option('blurb')) {
		$blurb = $prefix. $blurb. $suffix;
	}
	
	return $blurb;
}

?>