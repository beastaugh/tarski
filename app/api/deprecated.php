<?php
/**
 * @package WordPress
 * @subpackage Tarski
 *
 * The scrapyard: deprecated functions that haven't yet been removed.
 *
 * Don't write plugins that rely on these functions, as they are liable
 * to be removed between versions. There will usually be a better way
 * to do what you want; post on the forum if you need help.
 * @link http://tarskitheme.com/forum/
 */

/**
 * If debug mode is enabled, use uncompressed (development mode) JavaScript.
 *
 * @since 2.7
 * @deprecated 3.1.0
 *
 * @see TARSKI_DEBUG
 * @uses _tarski_compressible_asset_path
 *
 * @param string $path
 * @return string
 */
function tarski_js($path) {
    _deprecated_function(__FUNCTION__, '3.1.0');
    
    return tarski_asset_path($path);
}

/**
 * If debug mode is enabled, use uncompressed (development mode) CSS.
 *
 * @since 2.7
 * @deprecated 3.1.0
 *
 * @see TARSKI_DEBUG
 * @uses _tarski_compressible_asset_path
 *
 * @param string $path
 * @return string
 */
function tarski_css($path) {
    _deprecated_function(__FUNCTION__, '3.1.0');
    
    return tarski_asset_path($path);
}

/**
 * Adds JavaScript to the Tarski Options page.
 *
 * @since 1.4
 * @deprecated 3.1.0
 *
 * @uses get_bloginfo
 * @uses wp_enqueue_script
 *
 * @return void
*/
function tarski_inject_scripts() {
    _deprecated_function(__FUNCTION__, '3.1.0');
}

/**
 * Returns the number of authors who have published posts.
 *
 * @since 2.0.3
 * @deprecated 3.1.0
 *
 * @global object $wpdb
 * @return integer
 */
function tarski_count_authors() {
    _deprecated_function(__FUNCTION__, '3.1.0');
    
    global $wpdb;
    return count($wpdb->get_col($wpdb->prepare(
        "SELECT post_author, COUNT(DISTINCT post_author)
         FROM $wpdb->posts
         WHERE post_status = 'publish'
         GROUP BY post_author"
    ), 1));
}

/**
 * Determines whether Tarski should show authors.
 *
 * @since 2.0.3
 * @deprecated 3.1.0
 *
 * @uses tarski_count_authors()
 *
 * @global object $wpdb
 * @return boolean
 */
function tarski_should_show_authors() {
    _deprecated_function(__FUNCTION__, '3.1.0');
    
    $show_authors = tarski_count_authors() > 1;
    return (bool) apply_filters('tarski_show_authors', $show_authors);
}

/**
 * Re-saves Tarski's 'show_authors' option.
 *
 * If more than one author is detected, it will turn the 'show_authors'
 * option on; otherwise it will turn it off.
 *
 * @since 2.0.3
 * @deprecated 3.1.0
 *
 * @uses tarski_should_show_authors
 */
function tarski_resave_show_authors() {
    _deprecated_function(__FUNCTION__, '3.1.0');
    
    if (get_option('tarski_options')) {
        update_tarski_option('show_authors', tarski_should_show_authors());
    }
}

?>