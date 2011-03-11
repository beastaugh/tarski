<?php

/**
 * only_paginate_home() - Turns off paging for everything except feeds and the home page.
 * 
 * @since 2.2
 * @param object $query
 */
function only_paginate_home($query) {
	if ( !get_tarski_option('use_pages') && !is_admin() ) {
		if ( !is_home() && !is_feed() && '' === $query->get('nopaging') ) {
			$query->set('nopaging', 1);
		}
	}
}

/**
 * Returns the URI of the current alternate stylesheet.
 *
 * @uses get_tarski_option
 * @uses get_template_directory_uri
 * @uses get_stylesheet_directory_uri
 * @uses get_current_theme
 *
 * @return string
 */
function _tarski_get_alternate_stylesheet_uri() {
    $style = get_tarski_option('style');
    $path  = get_template_directory_uri();
    
    if (is_string($style) && strlen($style) > 0) {
        $file  = $style;
    } elseif (is_array($style)) {
        if ($style[0] == get_current_theme()) {
            $path = get_stylesheet_directory_uri();
            $file = $style[1];
        } elseif ('Tarski' == $style[0]) {
            $file = $style[1];
        }
    }
    
    return isset($file) ? $path . '/styles/' . $file : '';
}

/**
 * tarski_doctitle() - Returns the document title.
 * 
 * The order (site name first or last) can be set on the Tarski Options page.
 * While the function ultimately returns a string, please note that filters
 * are applied to an array! This allows plugins to easily alter any aspect
 * of the title. For example, one might write a plugin to change the separator.
 * @since 1.5
 * @param string $sep
 * @return string $doctitle
 * @hook filter tarski_doctitle
 * Filter document titles.
 */
function tarski_doctitle($sep = '&middot;') {
	$site_name = get_bloginfo('name');
	$content = trim(wp_title('', false));
	
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
	
	if (strlen($content))
		$elements = array(
			'site_name' => $site_name,
			'separator' => $sep,
			'content' => $content);
	else
		$elements = array('site_name' => $site_name);
	
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
 * @uses theme_version
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
    
    $themeversion = theme_version();
    $meta         = array(
        'wp_theme' => "<meta name=\"wp_theme\" content=\"Tarski $themeversion\">");
    $excerpt      = ($wp_query->post) ?
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
 * @see tarski_javascript
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
            'url' => get_bloginfo('stylesheet_url')),
        'print' => array(
            'url' => get_template_directory_uri() . '/library/css/print.css',
            'media' => 'print'));
    
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
 * Generate script elements linking to Tarski's JavaScript.
 *
 * @since 2.7
 *
 * @uses get_bloginfo
 * @uses site_url
 * @uses _tarski_asset_output
 *
 * @see tarski_meta
 * @see tarski_stylesheets
 *
 * @return void
 *
 * @hook filter tarski_javascript
 * Filter script elements before they're printed to the document.
 */
function tarski_javascript() {
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
 * @return string
 */
function tarski_headerimage() {
    if (get_theme_mod('header_image')) {
        $header_img_url = get_header_image();
    } else {
        $header = get_tarski_option('header');
        $file   = 'greytree.jpg';
        $path   = get_template_directory_uri();
        
        if (is_string($header) && strlen($header) > 0) {
            $file = $header;
        } elseif (is_array($header)) {
            if ($header[0] == get_current_theme()) {
                $path = get_stylesheet_directory_uri();
                $file = $header[1];
            } elseif ('Tarski' == $header[0]) {
                $file = $header[1];
            }
        }
        
        if ('blank.gif' == $file) return '';
        
        $header_img_url = $path . '/headers/' . $file;
    }
    
	$header_img_tag = sprintf('<img alt="%s" src="%s">',
		get_tarski_option('display_title')
		    ? __('Header image', 'tarski')
		    : get_bloginfo('name'),
		$header_img_url);
	
	if (!(get_tarski_option('display_title') || is_front_page()))
		$header_img_tag = sprintf(
			'<a title="%s" rel="home" href="%s">%s</a>',
			__('Return to main page', 'tarski'),
			user_trailingslashit(home_url()),
			$header_img_tag);
	
	echo "<div id=\"header-image\">$header_img_tag</div>\n\n";
}

/**
 * tarski_sitetitle() - Returns site title, wrapped in appropriate markup.
 * 
 * The title on the home page will appear inside an h1 element,
 * whereas on other pages it will be a link (to the home page),
 * wrapped in a p (paragraph) element.
 * @since 1.5
 * @return string
 * @hook filter tarski_sitetitle
 * Filter site title.
 */
function tarski_sitetitle() {
	if(get_tarski_option('display_title')) {
		$site_title = get_bloginfo('name');
		
		if(!is_front_page()) {
			$site_title = sprintf(
				'<a title="%1$s" href="%2$s" rel="home">%3$s</a>',
				__('Return to main page','tarski'),
				user_trailingslashit(home_url()),
				$site_title
			);
		}
		
		if((get_option('show_on_front') == 'posts') && is_home()) {
			$site_title = sprintf('<h1 id="blog-title">%s</h1>', $site_title);
		} else {
			$site_title = sprintf('<p id="blog-title">%s</p>', $site_title);
		}
		
		$site_title = apply_filters('tarski_sitetitle', $site_title);
		return $site_title;
	}
}

/**
 * tarski_tagline() - Returns site tagline, wrapped in appropriate markup.
 * 
 * @since 1.5
 * @return string
 * @hook filter tarski_tagline
 * Filter site tagline.
 */
function tarski_tagline() {
	$tagline = get_bloginfo('description', 'display');
	$tagline = (get_tarski_option('display_tagline') && strlen($tagline)) ? sprintf('<p id="tagline">%s</p>', $tagline) : '';
	return apply_filters('tarski_tagline', $tagline);
}

/**
 * tarski_titleandtag() - Outputs site title and tagline.
 * 
 * @since 1.5
 * @uses tarski_tagline()
 * @uses tarski_sitetitle()
 * @return string
 */
function tarski_titleandtag() {
	$title = tarski_sitetitle();
	$tagline = tarski_tagline();
	
	if ($title || $tagline)
		echo "<div id=\"title\">\n\t$title\n\t$tagline</div>\n";
}

/**
 * navbar_wrapper() - Outputs navigation section.
 * 
 * @uses th_navbar()
 * @since 2.1
 * @return string
 */
function navbar_wrapper() {
	echo '<div id="navigation" class="clearfix">';
	th_navbar();
	echo '</div>';
}

/**
 * home_link_name() - Returns the name for the navbar 'Home' link.
 * 
 * The option 'home_link_name' can be set in the Tarski Options page;
 * if it's not set, it defaults to 'Home'.
 * @since 1.7
 * @return string
 */
function home_link_name() {
	$default = __('Home','tarski');
	$option = get_tarski_option('home_link_name');	
	$name = (strlen($option)) ? $option : $default;
	return $name;
}

/**
 * Outputs the Tarski navbar.
 *
 * @since 1.2
 *
 * @uses apply_filters
 *
 * @param boolean $return
 * @return string $navbar
 *
 * @hook filter tarski_navbar
 * Filter the HTML generated for the navbar.
 */
function tarski_navbar($return = false) {
	$navbar = apply_filters('tarski_navbar', array());
    
	$navbar = is_array($navbar) && !empty($navbar)
		? sprintf("\n%s\n\n", implode("\n", $navbar))
		: '';
	
	if ($return)
		return $navbar;
	else
		echo $navbar;
}

/**
 * Adds a 'Home' link to the navbar.
 *
 * @see tarski_navbar
 * @uses home_link_name
 *
 * @param array $navbar
 * @return array $navbar
 */
function _tarski_navbar_home_link($navbar) {
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
 * @see tarski_navbar
 * @uses get_permalink
 *
 * @global object $wpdb
 * @param array $navbar
 * @return array $navbar
 */
function _tarski_navbar_page_links($navbar) {
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
 * @see _tarski_navbar_page_links
 * @uses is_page
 * @uses is_home
 *
 * @param integer
 * @return boolean
 */
function _tarski_on_page($id) {
     return is_page($id) ||
            ((get_option('show_on_front') == 'page') &&
             (get_option('page_for_posts') == $id) &&
             is_home());
}

/**
 * Adds external links to the navbar.
 * 
 * @since 2.0
 *
 * @see tarski_navbar
 * @uses get_bookmarks
 *
 * @param array $navbar
 * @return array $navbar
 */
function add_external_links($navbar) {
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
 *
 * @see tarski_navbar
 * @uses is_user_logged_in
 * @uses admin_url
 *
 * @param array $navbar
 * @return array $navbar
 */
function add_admin_link($navbar) {
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
 * @see tarski_navbar
 * @param string $navbar
 * @return string $navbar
 */
function wrap_navlist($navbar) {
    if (is_array($navbar)) {
        array_unshift($navbar, '<ul class="primary xoxo">');
        array_push($navbar, '</ul>');
    } else {
        $navbar = '';
    }
    
    return $navbar;
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
 * @since 2.8
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
 * tarski_bodyid() - Outputs the id that should be applied to the document body.
 * 
 * @since 1.7
 * @param boolean $return
 * @global object $post
 * @global object $wp_query
 * @return string $body_id
 * @hook filter tarski_bodyid
 * Filter the document id value.
 */
function tarski_bodyid($return = false) {
	global $post, $wp_query;

	if(is_home()) {
		$body_id = 'home';
	} elseif(is_search()) {
		$body_id = 'search';
	} elseif(is_page()) {
		$body_id = 'page-'. $post->post_name;
	} elseif(is_single()) {
		$body_id = 'post-'. $post->post_name;
	} elseif(is_category()) {
		$cat_ID = intval(get_query_var('cat'));
		$category = &get_category($cat_ID);
		$body_id = 'cat-'. $category->category_nicename;
	} elseif(is_tag()) {
		$tag_ID = intval(get_query_var('tag_id'));
		$tag = &get_term($tag_ID, 'post_tag');
		$body_id = 'tag-'. $tag->slug;
	} elseif(is_author()) {
		$author = the_archive_author();
		$body_id = 'author-'. $author->user_login;
	} elseif(is_date()) {
		$year = get_query_var('year');
		$monthnum = get_query_var('monthnum');
		$day = get_query_var('day');
		$body_id = "date";
		if(is_year()) {
			$body_id .= '-'. $year;
		} elseif(is_month()) {
			$body_id .= '-'. $year. '-'. $monthnum;
		} elseif(is_day()) {
			$body_id .= '-'. $year. '-'. $monthnum. '-'. $day;
		}
	} elseif(is_404()) {
		$body_id = '404';
	} else {
		$body_id = 'unknown';
	}
	
	$body_id = apply_filters('tarski_bodyid', $body_id);
	
	if($return)
		return $body_id;
	else
		echo $body_id;
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