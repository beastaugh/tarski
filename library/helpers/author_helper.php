<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * If the site has more than one author, output a link to the current post's
 * author's archive page.
 *
 * @since 1.7
 *
 * @uses is_multi_author
 * @uses get_author_posts_url
 * @uses get_the_author
 *
 * @see tarski_post_metadata
 *
 * @param string $metadata
 * @global object $authordata
 * @return string
 *
 * @hook filter tarski_show_authors
 * Allows other components to decide whether or not Tarski should show authors.
 */
function tarski_author_posts_link($metadata) {
    global $authordata;
    
    if ((bool) apply_filters('tarski_show_authors', is_multi_author())) {
        $link = sprintf('<a href="%1$s" title="%2$s" class="url fn">%3$s</a>',
            get_author_posts_url($authordata->ID, $authordata->user_nicename),
            sprintf(__('Articles by %s','tarski'), esc_attr(get_the_author())),
            get_the_author());
        
        $metadata .= __(' by ','tarski') .
            "<span class=\"vcard author\">$link</span>";
    }
    
    return $metadata;
}

/**
 * Returns the author object associated with an author archive page.
 *
 * @since 1.7
 *
 * @global object $wp_query
 * @return object $current_author
 */
function the_archive_author() {
    global $wp_query;
    $current_author = $wp_query->get_queried_object();
    return $current_author;
}

/**
 * Returns the display name of the author associated with a given archive page.
 *
 * @since 1.7
 *
 * @return string
 */
function the_archive_author_displayname() {
    $current_author = the_archive_author();
    return $current_author->display_name;
}

/**
 * Returns the author description of the author associated with a given archive
 * page.
 *
 * @since 1.7
 *
 * @return string
 */
function the_archive_author_description() {
    $current_author = the_archive_author();
    return isset($current_author->user_description) ?
        $current_author->user_description : '';
}

?>