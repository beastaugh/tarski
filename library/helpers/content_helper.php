<?php

/**
 * tarski_next_prev_posts() - Outputs links to the next and previous posts.
 * 
 * WordPress has this functionality, but the built-in formatting isn't
 * to Tarski's tastes, so this function builds its own.
 * @since 1.2
 * @return string
 */
function tarski_next_prev_posts() {
	if ( is_single() ) {
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
	$date = mysql2date(get_option('date_format'), $post->post_date);
	return apply_filters('tarski_date', $date);
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
 * tarski_asides_permalink_text() - Outputs permalink text for asides.
 *
 * @since 2.1
 * @global object $post
 * @return string
 */
function tarski_asides_permalink_text() {
	global $post;
	if($post->comment_status == 'open' || $post->comment_count > 0) {
		comments_number(__('No comments','tarski'), __('1 comment','tarski'), __('% comments','tarski'));
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
 * tidy_avatars - Remove some of the cruft generated by get_avatar()
 * 
 * Adds proper alternate text for the image, replaces single quotes
 * with double ones for markup consistency, and removes the height
 * and width attributes so a naturally sized default image can be
 * employed (e.g. a 1x1 pixel transparent GIF so there appears to
 * be no default image).
 * @since 2.1
 * @param string $avatar
 * @param string $id_or_email
 * @param string $size
 * @param string $default
 * @return mixed
 */
function tidy_avatars($avatar, $id_or_email, $size, $default) {
	$url = get_comment_author_url();
	$author_alt = sprintf( __('%s&#8217;s avatar'), get_comment_author() );
	$avatar = preg_replace("/height='[\d]+' width='[\d]+'/", '', $avatar);
	$avatar = preg_replace("/'/", '"', $avatar);
	$avatar = preg_replace('/alt=""/', "alt=\"$author_alt\"", $avatar);
	
	if( !empty($url) && 'http://' != $url )
		$avatar = "<a href=\"$url\">$avatar</a>";
		
	return $avatar;
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

	$output = rtrim($output, " \n\t\r\0\x0B");
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

?>