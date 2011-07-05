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

/**
 * wrap_values_in_element() - Wraps array values in the specified HTML element
 *
 * Given the array <code>array('Bread', 'Milk', 'Cheese')</code>, if the specified
 * HTML element were <code>'li'</code> it would return the array
 * <code>array('<li>Bread</li>', '<li>Milk</li>', '<li>Cheese</li>')</code>.
 * @since 2.0
 * @deprecated 3.0
 * @param $array array
 * @param $element string
 * @return array
 */
function wrap_values_in_element($array, $element) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    if (!is_array($array) || empty($array))
        return;
    
    foreach($array as $value)
        $output[] = "<$element>$value</$element>";
    
    return $output;
}

/**
 * Turns off paging for everything except feeds and the home page.
 *
 * @since 2.2
 * @deprecated 3.0
 * @param object $query
 */
function only_paginate_home($query) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    if ( !get_tarski_option('use_pages') && !is_admin() ) {
        if ( !is_home() && !is_feed() && '' === $query->get('nopaging') ) {
            $query->set('nopaging', 1);
        }
    }
}

/**
 * Outputs a text field and associated label.
 *
 * Used in the comments reply form to reduce duplication and clean up the
 * template. Adds a wrapper div around the label and input field for ease of
 * styling.
 *
 * @since 2.4
 * @deprecated 3.0
 * @uses required_field
 *
 * @param string $field
 * @param string $label
 * @param string $value
 * @param boolean $required
 * @param integer $size
 */
function comment_text_field($field, $label, $value = '', $required = false, $size = 20, $type = "text") {
    _deprecated_function(__FUNCTION__, '3.0'); ?>
    <div class="text-wrap <?php echo "$field-wrap"; ?>">
        <label for="<?php echo $field; ?>"><?php printf($label, required_field($required)); ?></label>
        <input class="<?php echo comment_field_classes(); ?>" type="<?php echo $type ?>" name="<?php echo $field; ?>" id="<?php echo $field; ?>" value="<?php echo $value; ?>" size="<?php echo $size; ?>"<?php if ($required) echo ' aria-required="true"'; ?>>
    </div>
<?php }

/**
 * Builds the HTML classes for comment form text fields.
 *
 * @since 2.4
 * @deprecated 3.0
 *
 * @param string $classes
 * @param boolean $required
 * @return string
 */
function comment_field_classes($classes = '', $required = false) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    $classes = trim($classes);
    if (strlen($classes) < 1) $classes = 'text';
    if ($required) $classes .= ' required';
    return apply_filters('comment_field_classes', $classes, $required);
}

/**
 * Returns a notice stating that a field is required.
 *
 * Thrown into a function for reusability's sake, and to reduce the number of
 * sprintf()s and localisation strings cluttering up the comment form.
 *
 * @since 2.4
 * @deprecated 3.0
 *
 * @param boolean $required
 * @return string
 */
function required_field($required = true) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    if ($required) return sprintf(
        '<span class="req-notice">(%s)</span>',
        __('required', 'tarski'));
}

/**
 * home_link_name() - Returns the name for the navbar 'Home' link.
 *
 * The option 'home_link_name' can be set in the Tarski Options page;
 * if it's not set, it defaults to 'Home'.
 * @since 1.7
 * @deprecated 3.0
 * @return string
 */
function home_link_name() {
    _deprecated_function(__FUNCTION__, '3.0');
    
    $default = __('Home','tarski');
    $option = get_tarski_option('home_link_name');
    $name = (strlen($option)) ? $option : $default;
    return $name;
}

/**
 * Adds a 'Home' link to the navbar.
 *
 * @deprecated 3.0
 *
 * @see tarski_navbar
 * @uses home_link_name
 *
 * @param array $navbar
 * @return array $navbar
 */
function _tarski_navbar_home_link($navbar) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    if (!is_array($navbar)) $navbar = array();
    
    if (get_option('show_on_front') != 'page')
        $navbar['home'] = sprintf(
            '<li><a id="nav-home"%1$s href="%2$s" rel="home">%3$s</a></li>',
            is_home() ? ' class="nav-current"' : '',
            user_trailingslashit(home_url()),
            home_link_name());
    
    return $navbar;
}

/**
 * Adds page links to the navbar.
 *
 * @deprecated 3.0
 *
 * @see tarski_navbar
 * @uses get_permalink
 *
 * @global object $wpdb
 * @param array $navbar
 * @return array $navbar
 */
function _tarski_navbar_page_links($navbar) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    global $wpdb;
    
    if (!is_array($navbar)) $navbar = array();
    
    $pages     = &get_pages('sort_column=post_parent,menu_order');
    $nav_pages = explode(',', get_tarski_option('nav_pages'));
    
    if (empty($nav_pages) || empty($pages)) return $navbar;
    
    foreach ($pages as $page) {
        if (!in_array($page->ID, $nav_pages)) continue;
        
        $page_status = _tarski_on_page($page->ID)
                     ? ' class="nav-current"'
                     : '';
        
        $navbar['page-' . $page->ID] = sprintf(
            '<li><a id="nav-%1$s"%2$s href="%3$s">%4$s</a></li>',
            $page->ID . '-' . $page->post_name,
            $page_status,
            get_permalink($page->ID),
            htmlspecialchars($page->post_title));
    }
    
    return $navbar;
}

/**
 * Utility function to determine whether the user is viewing a particular page.
 *
 * @deprecated 3.0
 *
 * @see _tarski_navbar_page_links
 * @uses is_page
 * @uses is_home
 *
 * @param integer
 * @return boolean
 */
function _tarski_on_page($id) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    return is_page($id) ||
           ((get_option('show_on_front') == 'page') &&
            (get_option('page_for_posts') == $id) &&
            is_home());
}

/**
 * Adds external links to the navbar.
 *
 * @since 2.0
 * @deprecated 3.0
 *
 * @see tarski_navbar
 * @uses get_bookmarks
 *
 * @param array $navbar
 * @return array $navbar
 */
function add_external_links($navbar) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    if (!is_array($navbar)) $navbar = array();
    
    if (!get_tarski_option('nav_extlinkcat')) return $navbar;
    
    $extlinks_cat = get_tarski_option('nav_extlinkcat');
    $extlinks = get_bookmarks("category=$extlinks_cat&orderby=rating");
    $target = $rel = '';
    $title  = '';
    foreach ($extlinks as $link) {
        if ($link->link_rel)
            $rel = 'rel="' . $link->link_rel . '" ';
        
        if ($link->link_target)
            $target = 'target="' . $link->link_target . '" ';
        
        if ($link->link_description)
            $title = 'title="'. $link->link_description . '" ';
        
        $navbar['link-' . $link->link_id] = sprintf(
            '<li><a id="nav-link-%1$s" %2$s href="%3$s">%4$s</a></li>',
            $link->link_id,
            $rel . $target . $title,
            $link->link_url,
            $link->link_name);
    }
    
    return $navbar;
}

/**
 * Adds a WordPress dashboard link to the Tarski navbar.
 *
 * @since 2.0
 * @deprecated 3.0
 *
 * @see tarski_navbar
 * @uses is_user_logged_in
 * @uses admin_url
 *
 * @param array $navbar
 * @return array $navbar
 */
function add_admin_link($navbar) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    if (is_user_logged_in())
        $navbar['admin'] = sprintf(
            '<li><a id="nav-admin" href="%1$s" title="%3$s">%2$s</a></li>',
            admin_url(), __('Dashboard &rarr;', 'tarski'),
            __('View your dashboard', 'tarski'));
    
    return $navbar;
}

/**
 * Wraps the Tarski navbar in an unordered list element.
 *
 * Unlike other navbar filters, wrap_navlist doesn't make $navbar an array
 * if it isn't one, since that would result in it outputting an empty
 * unordered list. Instead, it simply returns false.
 *
 * @since 2.0
 * @deprecated 3.0
 * @see tarski_navbar
 * @param string $navbar
 * @return string $navbar
 */
function wrap_navlist($navbar) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    if (is_array($navbar)) {
        array_unshift($navbar, '<ul class="primary xoxo">');
        array_push($navbar, '</ul>');
    } else {
        $navbar = '';
    }

    return $navbar;
}

/**
 * Generate script elements linking to Tarski's JavaScript.
 *
 * @since 2.7
 * @deprecated 3.0
 *
 * @uses get_bloginfo
 * @uses site_url
 * @uses _tarski_asset_output
 *
 * @see tarski_meta
 * @see tarski_stylesheets
 *
 * @return void
 */
function tarski_javascript() {
    _deprecated_function(__FUNCTION__, '3.0');
    
    $scripts = array();
    $files   = array(
        'tarski-js'     => get_template_directory_uri() . '/app/js/tarski.js',
        'comment-reply' => site_url('wp-includes/js/comment-reply.js'));
    
    foreach ($files as $name => $url) {
        $scripts[$name] = "<script type=\"text/javascript\" src=\"$url\"></script>";
    }
    
    _tarski_asset_output('javascript', $scripts);
}

/**
 * tarski_searchform() - Outputs the WordPress search form.
 *
 * Will only output the search form on pages that aren't a search
 * page or a 404, as these pages include the search form earlier
 * in the document and the search form relies on the 's' id value,
 * which as an HTML id must be unique within the document.
 * @since 2.0
 * @deprecated 3.0
 */
function tarski_searchform() {
    _deprecated_function(__FUNCTION__, '3.0');
    
    get_search_form();
}

/**
 * detectWPMUadmin() - Detect whether the current user is a WPMU site administrator.
 *
 * @since 2.0
 * @deprecated 3.0
 * @return boolean
 */
function detectWPMUadmin() {
    _deprecated_function(__FUNCTION__, '3.0');
    
    return is_multisite() && is_super_admin();
}

/**
 * Detect whether WordPress Multi-User is in use.
 *
 * @since 1.4
 * @deprecated 3.0
 * @return boolean
 */
function detectWPMU() {
    _deprecated_function(__FUNCTION__, '3.0');
    
    return function_exists('is_site_admin');
}

/**
 * Returns the classes that should be applied to the document body.
 *
 * @since 1.2
 * @deprecated 3.0
 *
 * @uses get_tarski_option
 * @uses is_valid_tarski_style
 * @uses get_bloginfo
 * @uses apply_filters
 *
 * @param boolean $return
 * @return string $classes
 */
function tarski_bodyclass($return = false) {
    _deprecated_function(__FUNCTION__, '3.0');
    
    if (get_tarski_option('centred_theme'))
        $classes[] = 'centre';
    
    if (get_tarski_option('swap_sides'))
        $classes[] = 'janus';
    
    if (get_tarski_option('style')) {
        $style = get_tarski_option('style');
        $file  = is_array($style) ? $style[1] : $style;
        
        if (is_valid_tarski_style($file))
            $classes[] = preg_replace('/^(.+)\.css$/', '\\1', $file);
    }
    
    if (get_bloginfo('text_direction') == 'rtl')
        $classes[] = 'rtl';
    
    $classes = apply_filters('tarski_bodyclass', $classes);
    $classes = is_array($classes) ? implode(' ', $classes) : '';
    
    if ($return)
        return $classes;
    else
        echo $classes;
}

?>