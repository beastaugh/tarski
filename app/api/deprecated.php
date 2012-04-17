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
 * Return the current theme version.
 *
 * @since 2.0
 * @deprecated 3.2.0
 *
 * @return string
 */
function theme_version() {
    _deprecated_function('wp_get_theme', '3.2.0');
    
    return wp_get_theme()->Version;
}

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

?>