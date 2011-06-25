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
 *
 * @uses previous_post_link
 * @uses next_post_link
 *
 * @return string
 */
function tarski_next_prev_posts() {
    if (is_single()) {
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
 * Passes some Tarski-specific arguments to wp_link_pages.
 *
 * @since 2.0
 *
 * @uses wp_link_pages
 */
function tarski_link_pages() {
    $arguments = array(
        'before'           => '<p class="link-pages"><strong>' .
                              __('Pages:','tarski') .
                              '</strong>',
        'after'            => '</p>',
        'next_or_number'   => 'number',
        'nextpagelink'     => __('Next page','tarski'),
        'previouspagelink' => __('Previous page','tarski'),
        'pagelink'         => '%',
        'more_file'        => '',
        'echo'             => true);
    
    if (!(has_post_format('aside') || in_category(get_tarski_option('asidescategory'))))
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
    if (is_singular()) return;
    
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
 * A simple wrapper around the get_the_category_list function, it wraps the
 * categories list in a span to make it easier to access via the DOM.
 *
 * @since 2.0
 *
 * @uses get_tarski_option
 * @uses get_the_category_list
 *
 * @see tarski_post_metadata
 *
 * @param string $metadata
 * @return string
 */
function tarski_post_categories_link($metadata) {
    if (get_tarski_option('show_categories')) {
        $cats = get_the_category_list(', ');
        
        if (strlen($cats)) {
            $metadata .= sprintf(__(' in %s','tarski'),
                '<span class="categories">' . $cats . '</span>');
        }
    }
    
    return $metadata;
}

/**
 * A specialisation of the core function comments_number, written mainly
 * because the core function echoes its result rather than returning it.
 *
 * @since 2.7
 *
 * @uses get_comments_number
 * @uses number_format_i18n
 * @uses comments_number
 *
 * @see comments_number
 * @see tarski_comments_link
 *
 * @global integer $id
 * @return string
 */
function tarski_comments_number() {
    global $id;
    
    $number = get_comments_number($id);
    
    if ($number > 1) {
        $output = str_replace('%',
            number_format_i18n($number),
            __('% comments', 'tarski'));
    } elseif ($number == 0) {
        $output = __('No comments', 'tarski');
    } else {
        $output = __('1 comment', 'tarski');
    }
    
    return apply_filters('comments_number', $output, $number);
}

/**
 * Returns a link to a post's comments (if a post has them) or the comment form
 * (if comments are open).
 *
 * @since 2.1
 *
 * @uses comments_open
 * @uses get_permalink
 * @uses comments_number
 *
 * @see tarski_post_metadata
 *
 * @param string $metadata
 * @global object $post
 * @return string
 */
function tarski_comments_link($metadata) {
    global $post;
    
    $have_comments = intval($post->comment_count) > 0;
    
    if (comments_open() || $have_comments) {
        $href = get_permalink() . ($have_comments ? '#comments' : '#respond');
        $text = tarski_comments_number();
    } else {
        $href = get_permalink();
        $text = __('Permalink', 'tarski');
    }
    
    $link = sprintf('<a class="comments-link" href="%s">%s</a>', $href, $text);
                         
    return $metadata . ' | ' . $link;
}

/**
 * Outputs permalink text for asides.
 *
 * @since 2.1
 *
 * @uses comments_number
 *
 * @global object $post
 * @return string
 */
function tarski_asides_permalink_text() {
    global $post;
    if ($post->comment_status == 'open' || $post->comment_count > 0) {
        comments_number(__('No comments','tarski'),
                        __('1 comment','tarski'),
                        __('% comments','tarski'));
    } else {
        _e('Permalink', 'tarski');
    }
}

/**
 * Returns HTML representing metadata associated with a post, e.g. the date and
 * time of posting, the author, the number of comments, an edit link etc. This
 * function is essentially a wrapper that returns the results of applying
 * filters via the th_post_metadata hook.
 *
 * @since 2.7
 *
 * @see tarski_post_metadata
 *
 * @return string
 *
 * @hook filter th_post_metadata
 * Allows for the customisation of post metadata (the content displayed
 * immediately below the post title). By default 
 */
function th_post_metadata() {
    return apply_filters('th_post_metadata', '');
}

/**
 * This function drives Tarski's post metadata, adding different filters to the
 * th_post_metadata hook depending on which kind of page is being viewed etc.
 * Depending on how its use evolves, we may have to revisit the way this
 * operates, since it runs before we know which individual posts it's being
 * used for.
 *
 * @since 2.7
 *
 * @uses is_attachment
 * @uses add_filter
 *
 * @see th_post_metadata
 * @see tarski_post_categories_link
 * @see tarski_author_posts_link
 * @see tarski_comments_link
 * @see tarski_post_metadata_edit
 * @see tarski_post_metadata_wrapper
 *
 * @return void
 */
function tarski_post_metadata() {
    $filters = array();
    
    if (!is_page()) {
        $filters[] = 'tarski_post_metadata_date';
    }
    
    if (!(is_attachment() || is_page())) {
        $filters[] = 'tarski_post_categories_link';
        $filters[] = 'tarski_author_posts_link';
        $filters[] = 'tarski_comments_link';
    }
    
    $filters[] = 'tarski_post_metadata_edit';
    $filters[] = 'tarski_post_metadata_wrapper';
    
    foreach ($filters as $filter) {
        add_filter('th_post_metadata', $filter);
    }
}

/**
 * Wraps a post's metadata in a paragraph.
 *
 * @since 2.7
 *
 * @see tarski_post_metadata
 *
 * @param string $metadata
 * @return string
 */
function tarski_post_metadata_wrapper($metadata) {
    // $wrapper_class = tarski_post_is_aside() ? 'meta' : 'metadata';
    $wrapper_class = 'metadata';
    return "<p class=\"${wrapper_class}\">" . $metadata . '</p>';
}

/**
 * Displays the date of a given post.
 *
 * @since 2.7
 *
 * @uses get_the_time
 * @uses get_option
 *
 * @see th_post_metadata
 * @see tarski_post_metadata
 *
 * @param string $metadata
 * @return string
 */
function tarski_post_metadata_date($metadata) {
    $date = sprintf('<span class="date updated">%s</span>',
        get_the_time(get_option('date_format')));
    
    return $metadata . $date;
}

/**
 * Displays edit links for a given post.
 *
 * @since 2.7
 *
 * @uses get_edit_post_link
 * @uses esc_attr
 * @uses esc_html
 *
 * @see th_post_metadata
 * @see tarski_post_metadata
 *
 * @param string $metadata
 * @global object $post
 * @return string
 */
function tarski_post_metadata_edit($metadata) {
    $uri = get_edit_post_link();
    
    if ($uri) {
        global $post;
        
        $edit_link = sprintf(
            '<a class="post-edit-link" href="%s" title="%s">%s</a>',
            $uri,
            esc_attr(__('Edit this post', 'tarski')),
            esc_html(__('edit', 'tarski')));
        
        $metadata .= ' <span class="edit">('
                  .  apply_filters('edit_post_link', $edit_link, $post->ID)
                  .  ')</span>';
    }
    
    return $metadata;
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
        '<a href="' . user_trailingslashit(home_url()) . '">' . __('front page', 'tarski') . '</a>'
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
 *
 * @uses wptexturize
 * @uses convert_chars
 * @uses wpautop
 *
 * @param string $text
 * @return string
 */
function tarski_content_massage($text) {
    if (strlen($text) > 0)
        return convert_chars(wptexturize($text));
}

?>