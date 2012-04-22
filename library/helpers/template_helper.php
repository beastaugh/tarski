<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Returns the URI of the current alternate stylesheet.
 *
 * @uses get_tarski_option
 * @uses get_template_directory_uri
 * @uses get_stylesheet_directory_uri
 * @uses wp_get_theme
 *
 * @return string
 */
function _tarski_get_alternate_stylesheet_uri() {
    $style = get_tarski_option('style');
    $path  = get_template_directory_uri();
    
    if (is_string($style) && strlen($style) > 0) {
        $file  = $style;
    } elseif (is_array($style)) {
        if ($style[0] == wp_get_theme()->Name) {
            $path = get_stylesheet_directory_uri();
            $file = $style[1];
        } elseif ('Tarski' == $style[0]) {
            $file = $style[1];
        }
    }
    
    return isset($file) ? $path . '/styles/' . $file : '';
}

/**
 * Creates the document title.
 *
 * The order (site name first or last) can be set on the Tarski Options page.
 * While the function ultimately returns a string, please note that filters
 * are applied to an array! This allows plugins to easily alter any aspect
 * of the title. For example, one might write a plugin to change the separator.
 *
 * @since 1.5
 *
 * @param string $sep
 * @return string
 *
 * @hook filter tarski_doctitle
 * Filter document titles.
 */
function tarski_document_title($title, $sep, $seplocation) {
    $title    = trim($title);
    $sitename = get_bloginfo('name');
    $enc      = get_option('blog_charset');
    
    if (!(isset($enc) && strlen($enc) > 0)) {
        $enc = "utf-8";
    }
    
    $slen     = mb_strlen($sep, $enc);
    $tlen     = mb_strlen($title, $enc);
    
    if ($seplocation == 'right') {
        $doctitle = mb_substr($title, 0, $tlen - $slen, $enc);
    } else {
        $doctitle = mb_substr($title, $slen, $tlen - $slen, $enc);
    }
    
    $doctitle = trim($doctitle);
    
    if (is_404()) {
        $doctitle = sprintf(__('Error %s', 'tarski'), '404');
    } elseif ((get_option('show_on_front') == 'posts') && is_home()) {
        $doctitle = get_bloginfo('description', 'display');
    } elseif (is_search()) {
        $doctitle = sprintf(__('Search results for %s', 'tarski'), esc_html(get_search_query()));
    } elseif (is_month()) {
        $doctitle = single_month_title(' ', false);
    } elseif (is_tag()) {
        $doctitle = multiple_tag_titles();
    }
    
    $title = array($sitename, $sep, $doctitle);
    
    if (get_tarski_option('swap_title_order')) {
        $title = array_reverse($title);
    }
    
    return implode(" ", $title);
}

/**
 * Remove unnecessary gallery styling.
 *
 * The gallery feature adds an inline style element, which is pretty horrible
 * in any case, but it also adds lots of unnecessary rules which Tarski has its
 * own equivalents for.
 *
 * One thing that is necessary is the width of each gallery item, since this is
 * set dynamically from within WordPress and cannot be calculated in advance or
 * otherwise accounted for by the theme.
 *
 * Consequently, this function removes the other rules from the style block,
 * while preserving the gallery item styling.
 *
 * @since 2.4
 *
 * @param string $style
 * @return string
 */
function trim_gallery_style($style) {
    $style = preg_replace('/\.gallery img {.*?}/s', '', $style);
    $style = preg_replace('/\.gallery {.*?}/s', '', $style);
    $style = preg_replace('/\.gallery-caption {.*?}/s', '', $style);
    return $style;
}

/**
 * Generate meta elements pertaining to Tarski and the site.
 *
 * @since 2.7
 *
 * @uses wp_get_theme
 * @uses get_bloginfo
 * @uses get_option
 * @uses _tarski_asset_output
 *
 * @see tarski_stylesheets
 * @see tarski_javascript
 *
 * @return void
 *
 * @hook filter tarski_asset_meta
 * Filters metadata (in the form of meta and link elements) that appears in the
 * document head.
 */
function tarski_meta() {
    global $wp_query;
    
    $themeversion = wp_get_theme()->Version;
    $excerpt      = (isset($wp_query->post)) ?
        trim(strip_tags(esc_attr($wp_query->post->post_excerpt))) : '';
    
    if (is_singular() && strlen($excerpt)) {
        $description = $excerpt;
    } else {
        $description = trim(strip_tags(get_bloginfo('description', 'display')));
    }
    
    if (strlen($description)) {
        $meta['description'] = sprintf('<meta name="description" content="%s">',
            wptexturize($description));
    }
    
    if (get_option('blog_public') != '0') {
        $meta['robots'] = '<meta name="robots" content="all">';
    }
    
    $meta['xfn_profile'] = '<link rel="profile" href="http://gmpg.org/xfn/11">';
    
    _tarski_asset_output('asset_meta', $meta);
}

/**
 * Enqueue Tarski's own JavaScript and the comment reply script.
 *
 * @since 3.1.3
 *
 * @uses wp_enqueue_script
 * @uses tarski_asset_path
 * @uses wp_get_theme
 *
 * @see tarski_meta
 * @see tarski_stylesheets
 *
 * @return void
 */
function tarski_enqueue_scripts() {
    wp_enqueue_script('tarski',
        tarski_asset_path('app/js/tarski.js'),
        array('jquery'), wp_get_theme()->Version);
    
    wp_enqueue_script('comment-reply');
}

/**
 * Generate links to the various Tarski stylesheets.
 *
 * @since 2.7
 *
 * @uses get_bloginfo
 * @uses get_tarski_option
 * @uses _tarski_get_alternate_stylesheet_uri
 * @uses _tarski_asset_output
 *
 * @see tarski_meta
 *
 * @return void
 *
 * @hook filter tarski_style_array
 * Filter the array of stylesheet attributes from which the stylesheet
 * links are generated.
 *
 * @hook filter tarski_stylesheets
 * Filter the raw stylesheet link elements before they're printed to
 * the document.
 */
function tarski_stylesheets() {
    $style_array = array(
        'main' => array(
            'url' => tarski_asset_path('style.css')),
        'print' => array(
            'url' => tarski_asset_path('library/css/print.css'),
            'media' => 'print'));
    
    if (get_template_directory() != get_stylesheet_directory()) {
        $style_array['child_main'] = array('url' => get_stylesheet_uri());
    }
    
    if (get_tarski_option('style')) {
        $style_uri = _tarski_get_alternate_stylesheet_uri();
        
        if (strlen($style_uri) > 0) {
            $style_array['alternate'] = array('url' => $style_uri);
        }
    }
    
    $style_array = apply_filters('tarski_style_array', $style_array);
    
    if (is_array($style_array)) {
        foreach ($style_array as $type => $values) {
            if (is_array($values) && $values['url']) {
                if (empty($values['media'])) {
                    $values['media'] = 'all';
                }
                
                $stylesheets[$type] = sprintf(
                    '<link rel="stylesheet" href="%1$s" type="text/css" media="%2$s">',
                    $values['url'],
                    $values['media']);
            }
        }
    }
    
    _tarski_asset_output('stylesheets', $stylesheets);
}

/**
 * Apply filters to an array of HTML elements, then print the result.
 *
 * @since 2.7
 *
 * @uses apply_filters
 *
 * @see tarski_meta
 * @see tarski_stylesheets
 * @see tarski_javascript
 *
 * @return void
 */
function _tarski_asset_output($type, $assets) {
    $filtered = apply_filters('tarski_' . $type, $assets);
    
    echo implode("\n", $filtered) . "\n\n";
}

/**
 * Outputs header image.
 *
 * @since 1.0
 *
 * @uses get_theme_mod
 * @uses get_header_image
 * @uses get_tarski_option
 * @uses get_bloginfo
 * @uses is_front_page
 * @uses user_trailingslashit
 * @uses home_url
 *
 * @global object $post
 *
 * @return string
 */
function tarski_headerimage() {
    global $post;
    
    if (!get_theme_mod('header_image')) return;
    
    $header_img_url = get_header_image();
    
    if (get_tarski_option('featured_header') &&
        is_singular($post) &&
        has_post_thumbnail($post->ID)) {
        $image_size = array(HEADER_IMAGE_WIDTH, HEADER_IMAGE_WIDTH);
        $image_id   = get_post_thumbnail_id($post->ID);
        $image      = wp_get_attachment_image_src($image_id, $image_size);
        
        if ($image[1] >= HEADER_IMAGE_WIDTH) {
            $header_img_tag = get_the_post_thumbnail($post->ID, $image_size);
        }
    }
    
    if (!$header_img_url) return;
    
    if (!isset($header_img_tag)) {
        $header_img_tag = sprintf('<img alt="%s" src="%s">',
            get_tarski_option('display_title')
                ? __('Header image', 'tarski')
                : get_bloginfo('name'),
            $header_img_url);
    }
    
    if (!(get_tarski_option('display_title') || is_front_page()))
        $header_img_tag = sprintf(
            '<a title="%s" rel="home" href="%s">%s</a>',
            __('Return to main page', 'tarski'),
            user_trailingslashit(home_url()),
            $header_img_tag);
    
    echo "<div id=\"header-image\">$header_img_tag</div>\n\n";
}

/**
 * Returns site title, wrapped in appropriate markup.
 *
 * The title on the home page will appear inside an h1 element,
 * whereas on other pages it will be a link (to the home page),
 * wrapped in a p (paragraph) element.
 *
 * @since 1.5
 *
 * @return string
 *
 * @hook filter tarski_sitetitle
 * Filter site title.
 */
function tarski_sitetitle() {
    if (!get_tarski_option('display_title')) return '';
    
    $site_title = get_bloginfo('name');
    
    if (!is_front_page()) {
        $site_title = sprintf(
            '<a title="%1$s" href="%2$s" rel="home">%3$s</a>',
            __('Return to main page','tarski'),
            user_trailingslashit(home_url()),
            $site_title);
    }
    
    if ((get_option('show_on_front') == 'posts') && is_home()) {
        $site_title = sprintf('<h1 id="blog-title">%s</h1>', $site_title);
    } else {
        $site_title = sprintf('<p id="blog-title">%s</p>', $site_title);
    }
    
    return apply_filters('tarski_sitetitle', $site_title);
}

/**
 * Returns site tagline, wrapped in appropriate markup.
 *
 * @since 1.5
 *
 * @return string
 *
 * @hook filter tarski_tagline
 * Filter site tagline.
 */
function tarski_tagline() {
    $tagline = get_bloginfo('description', 'display');
    $tagline = (get_tarski_option('display_tagline') && strlen($tagline)) ? sprintf('<p id="tagline">%s</p>', $tagline) : '';
    return apply_filters('tarski_tagline', $tagline);
}

/**
 * Outputs site title and tagline.
 *
 * @since 1.5
 *
 * @uses tarski_tagline
 * @uses tarski_sitetitle
 *
 * @return string
 */
function tarski_titleandtag() {
    $title = tarski_sitetitle();
    $tagline = tarski_tagline();
    
    if ($title || $tagline)
        echo "<div id=\"title\">\n\t$title\n\t$tagline</div>\n";
}

/**
 * Outputs navigation section.
 *
 * @since 2.1
 *
 * @uses th_navbar
 *
 * @return string
 */
function navbar_wrapper() {
    echo '<div id="navigation" class="clearfix">';
    th_navbar();
    echo '</div>';
    
    echo <<<NAVBAR_SCRIPT
<script type="text/javascript">
    jQuery(document).ready(function() {
        var container = jQuery('#navigation > ul'),
            navbar;
        
        if (container.length > 0) {
            navbar = new Tarski.Navbar(container);
        }
    });
</script>
NAVBAR_SCRIPT;
}

/**
 * Outputs the Tarski navbar.
 *
 * @since 1.2
 *
 * @uses wp_nav_menu
 * @see tarski_default_navbar
 *
 * @return void
 */
function tarski_navbar() {
    wp_nav_menu(array(
        'theme_location' => 'tarski_navbar',
        'container'      => false,
        'menu_class'     => 'primary xoxo',
        'fallback_cb'    => 'tarski_default_navbar'));
}

/**
 * Output a default set of navbar links. Used as a fallback if the user hasn't
 * defined their own navbar.
 *
 * @since 3.1
 *
 * @uses is_home
 * @uses home_url
 * @uses wp_list_pages
 *
 * @return void
 */
function tarski_default_navbar() {
    $home_current = is_home() ? ' class="current_page_item"' : '';
    $home_title   = __('Return to front page', 'tarski');
    $home_name    = __('Home', 'tarski');
    $home_url     = home_url();
    
    $navbar_pages = wp_list_pages(array(
        'sort_column' => 'menu_order',
        'title_li'    => '',
        'depth'       => 1,
        'echo'        => 0));
    
    echo <<<NAVBAR_HTML
        <ul id="menu-primary-navigation" class="primary xoxo">
            <li$home_current><a title="$home_title" href="$home_url">$home_name</a></li>
            $navbar_pages
    </ul>
NAVBAR_HTML;
}

/**
 * Adds a feed link to the site navigation.
 *
 * @since 2.0
 *
 * @uses get_template_part
 *
 * @return void
 */
function tarski_feedlink() {
    get_template_part('app/templates/feed_link');
}

/**
 * Add Tarski-specific classes to the body element.
 *
 * @since 3.0
 *
 * @uses get_tarski_option
 * @uses is_valid_tarski_style
 * @uses get_bloginfo
 *
 * @param array $classes
 * @return array
 */
function tarski_body_class($classes) {
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
    
    return $classes;
}

/**
 * Returns the id that should be applied to the document body.
 *
 * @since 1.7
 *
 * @param boolean $return
 * @global object $post
 * @global object $wp_query
 * @return string
 *
 * @hook filter tarski_bodyid
 * Filter the document id value.
 */
function tarski_bodyid() {
    global $post, $wp_query;
    
    if (is_home()) {
        $body_id = 'home';
    } elseif (is_search()) {
        $body_id = 'search';
    } elseif (is_page()) {
        $body_id = 'page-'. $post->post_name;
    } elseif (is_single()) {
        $body_id = 'post-'. $post->post_name;
    } elseif (is_category()) {
        $cat_ID = intval(get_query_var('cat'));
        $category = &get_category($cat_ID);
        $body_id = 'cat-'. $category->category_nicename;
    } elseif (is_tag()) {
        $tag_ID = intval(get_query_var('tag_id'));
        $tag = &get_term($tag_ID, 'post_tag');
        $body_id = 'tag-'. $tag->slug;
    } elseif (is_author()) {
        $author = the_archive_author();
        $body_id = 'author-'. $author->user_login;
    } elseif (is_date()) {
        $year = get_query_var('year');
        $monthnum = get_query_var('monthnum');
        $day = get_query_var('day');
        $body_id = "date";
        if (is_year()) {
            $body_id .= '-'. $year;
        } elseif (is_month()) {
            $body_id .= '-'. $year. '-'. $monthnum;
        } elseif (is_day()) {
            $body_id .= '-'. $year. '-'. $monthnum. '-'. $day;
        }
    } elseif (is_404()) {
        $body_id = '404';
    } else {
        $body_id = 'unknown';
    }
    
    return apply_filters('tarski_bodyid', $body_id);
}

/**
 * A simple wrapper around WordPress' thumbnail functionality to allow them to
 * link to the relevant post from index pages.
 *
 * @since 2.6
 *
 * @uses get_the_post_thumbnail
 * @uses get_permalink
 * @uses is_single
 * @uses is_page
 *
 * @return string
 */
function tarski_post_thumbnail() {
    $wrapper   = '<a class="imagelink2" href="%s">%s</a>';
    $thumbnail = get_the_post_thumbnail(null, 'post-thumbnail', array('class' => 'imageright'));
    
    if (empty($thumbnail)) return '';
    
    return is_singular() ? $thumbnail : sprintf($wrapper, get_permalink(), $thumbnail);
}

/**
 * Outputs the site feed and Tarski credits.
 *
 * @since 1.5
 *
 * @uses get_template_part
 *
 * @return void
 */
function tarski_credits() {
    get_template_part('app/templates/credits');
}

?>