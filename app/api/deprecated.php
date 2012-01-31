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
 * Returns the document title.
 *
 * The order (site name first or last) can be set on the Tarski Options page.
 * While the function ultimately returns a string, please note that filters
 * are applied to an array! This allows plugins to easily alter any aspect
 * of the title. For example, one might write a plugin to change the separator.
 *
 * @since 1.5
 * @deprecated 3.2.0
 *
 * @param string $sep
 * @return string
 *
 * @hook filter tarski_doctitle
 * Filter document titles.
 */
function tarski_doctitle($sep = '&middot;') {
    _deprecated_function('wp_title', '3.2.0');
    
    $site_name = get_bloginfo('name');
    $content   = trim(wp_title('', false));
   
    if (is_404())
        $content = sprintf(__('Error %s', 'tarski'), '404');
    elseif ((get_option('show_on_front') == 'posts') && is_home())
        $content = get_bloginfo('description', 'display');
    elseif (is_search())
        $content = sprintf(__('Search results for %s', 'tarski'), esc_html(get_search_query()));
    elseif (is_month())
        $content = single_month_title(' ', false);
    elseif (is_tag())
        $content = multiple_tag_titles();
   
    $elements = strlen($content) > 0
              ? array('site_name' => $site_name,
                      'separator' => $sep,
                      'content'   => $content)
              : array('site_name' => $site_name);
   
    if (get_tarski_option('swap_title_order'))
        $elements = array_reverse($elements, true);
   
    // Filters should return an array
    $elements = apply_filters('tarski_doctitle', $elements);
   
    // But if they don't, it won't try to implode
    if (is_array($elements))
        $doctitle = implode(' ', $elements);
   
    echo $doctitle;
}

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