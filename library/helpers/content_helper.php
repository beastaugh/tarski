<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Outputs links to the next and previous posts.
 * 
 * WordPress has this functionality, but the built-in formatting isn't
 * to Tarski's tastes, so this function builds its own.
 * 
 * @since 1.2
 * @uses previous_post_link()
 * @uses next_post_link()
 * 
 * @return string
 */
function tarski_next_prev_posts() {
	if ( is_single() ) {
		$prev_post = get_previous_post();
		$next_post = get_next_post();
		
		if ($prev_post || $next_post) {
			echo '<p class="primary-span articlenav">';

			if ($prev_post) {
				echo '<span class="previous-entry">';
				previous_post_link('%link','&lsaquo; %title');
				echo '</span>';

				if ($next_post) {
					echo ' <span class="separator">&nbsp;&bull;&nbsp;</span> ';
				}
			}

			if ($next_post) {
				echo '<span class="next-entry">';
				next_post_link('%link','%title &rsaquo;');
				echo '</span>';
			}

			echo "</p>\n";
		}
	}
}

/**
 * Passes some Tarski-specific arguments to wp_link_pages().
 * 
 * @since 2.0
 * @uses wp_link_pages()
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
	
	if (!in_category(get_tarski_option('asidescategory')))
		wp_link_pages($arguments);
}

/**
 * Outputs next / previous index page links.
 * 
 * @since 1.2
 * 
 * @global object $wp_query
 * @return string
 */
function tarski_posts_nav_link() {
	if (!get_tarski_option('use_pages') || is_singular()) return;
	
	global $wp_query;
	
	$max_num_pages = $wp_query->max_num_pages;
	$paged = get_query_var('paged');
	
	if ($max_num_pages <= 1) return;
	
	if (is_search())
		$links = array(
			get_previous_posts_link('&laquo; ' . __('Previous results', 'tarski')),
			get_next_posts_link(__('More results', 'tarski') . ' &raquo;'));
	else
		$links = array(
			get_next_posts_link('&laquo; ' . __('Older entries', 'tarski')),
			get_previous_posts_link(__('Newer entries', 'tarski') . ' &raquo;'));
	
	printf('<p class="pagination">%1$s%3$s%2$s</p>',
		$links[0], $links[1],
		$paged < 2 || $paged >= $max_num_pages ? '' : ' &sect; ');
}

/**
 * A simple wrapper around get_the_category_list().
 * 
 * Wraps the categories list in a span to make it easier to access via the DOM.
 * 
 * @since 2.0
 * @uses get_the_category_list()
 * 
 * @return string
 */
function tarski_post_categories_link() {
	if (get_tarski_option('show_categories')) printf(__(' in %s','tarski'),
		'<span class="categories">' . get_the_category_list(', ') . '</span>');
}

/**
 * Outputs comments links for different post types.
 * 
 * The function has different output modes for single posts, pages and posts on
 * index pages; in the latter case it's a simple function call, but in the
 * former cases it has to be built manually and it's convenient to have a
 * wrapper around all the logic, so as to keep the templates clean.
 * 
 * @since 2.1
 * @uses comments_number()
 * @uses comments_popup_link()
 * 
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
 * Outputs permalink text for asides.
 *
 * @since 2.1
 * @uses comments_number()
 * 
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
 * Outputs default text for 404 error pages.
 *
 * @since 1.5
 * 
 * @return string
 * 
 * @hook filter th_404_content
 * Allows users to change their 404 page messages via a plugin.
 */
function tarski_404_content() {
	$content = sprintf(
		__('The page you are looking for does not exist; it may have been moved, or removed altogether. You might want to try the search function or return to the %s.','tarski'),
		'<a href="' . user_trailingslashit(get_bloginfo('url')) . '">' . __('front page', 'tarski') . '</a>'
	);
	$content = wpautop($content);
	$content = apply_filters('th_404_content', $content);
	echo $content;
}

/**
 * Filter adding smart quotes, auto-paragraphs etc.
 * 
 * This function strips slashes, adds smart quotes and other typographical
 * niceties, converts characters such as ampersands to their HTML equivalent,
 * adds automatic paragraphing and line breaks, and finally returns the
 * altered content.
 * 
 * @since 2.0.5
 * @uses wptexturize()
 * @uses convert_chars()
 * @uses wpautop
 * 
 * @param string $text
 * @return string
 */
function tarski_content_massage($text) {
	if (strlen($text) > 0)
		return wpautop(convert_chars(wptexturize(stripslashes($text))));
}

?>