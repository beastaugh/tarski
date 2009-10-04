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
		$content = sprintf(__('Search results for %s', 'tarski'), attribute_escape(get_search_query()));
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
 * tarski_headerimage() - Outputs header image.
 * 
 * @since 1.0
 * @return string
 */
function tarski_headerimage() {
	$header_opt = get_tarski_option('header');
	if ($header_opt == 'blank.gif') return;
	
	if (get_theme_mod('header_image'))
		$header_img_url = get_header_image();
	elseif (!empty($header_opt))
		$header_img_url = get_bloginfo('template_directory') . '/headers/' . $header_opt;
	else
		$header_img_url = get_bloginfo('template_directory') . '/headers/greytree.jpg';
	
	$header_img_tag = sprintf('<img alt="%s" src="%s" />',
		get_tarski_option('display_title') ? __('Header image', 'tarski') : get_bloginfo('name'),
		$header_img_url);
	
	if (!get_tarski_option('display_title') && !is_front_page())
		$header_img_tag = sprintf(
			'<a title="%s" rel="home" href="%s">%s</a>',
			__('Return to main page', 'tarski'),
			user_trailingslashit(get_bloginfo('url')),
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
				user_trailingslashit(get_bloginfo('url')),
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
 * tarski_navbar() - Outputs the Tarski navbar.
 * 
 * @since 1.2
 * @param boolean $return
 * @global object $wpdb
 * @return string $navbar
 * @hook filter tarski_navbar
 * Filter the HTML generated for the navbar.
 */
function tarski_navbar($return = false) {
	global $wpdb;
	
	$current = ' class="nav-current"';
	$navbar  = array();
	
	if (get_option('show_on_front') != 'page')
		$navbar['home'] = sprintf(
			'<li><a id="nav-home"%1$s href="%2$s" rel="home">%3$s</a></li>',
			is_home() ? $current : '',
			user_trailingslashit(get_bloginfo('url')),
			home_link_name());
	
	$pages = &get_pages('sort_column=post_parent,menu_order');
	$nav_pages = explode(',', get_tarski_option('nav_pages'));
	
	if (!empty($nav_pages) && !empty($pages)) {
		foreach ($pages as $page) {
			if (in_array($page->ID, $nav_pages)) {
				$page_status = is_page($page->ID) || ((get_option('show_on_front') == 'page') && (get_option('page_for_posts') == $page->ID) && is_home())
					? $current
					: '';
				
				$navbar[$page->ID] = sprintf(
					'<li><a id="nav-%1$s"%2$s href="%3$s">%4$s</a></li>',
					$page->ID . '-' . $page->post_name,
					$page_status,
					get_permalink($page->ID),
					htmlspecialchars($page->post_title)
				);
			}
		}
	}
	
	// Filters should return an array
	$navbar = apply_filters('tarski_navbar', $navbar);

	// But if they don't, the function will return false
	$navbar = is_array($navbar) && !empty($navbar)
		? "\n" . implode("\n", $navbar) . "\n\n"
		: false;
	
	if ($return)
		return $navbar;
	else
		echo $navbar;
}

/**
 * add_external_links() - Adds external links to the Tarski navbar.
 * 
 * @since 2.0
 * @see tarski_navbar()
 * @param array $navbar
 * @return array $navbar
 */
function add_external_links($navbar) {
	if(!is_array($navbar))
		$navbar = array();
	
	if(get_tarski_option('nav_extlinkcat')) {
		$extlinks_cat = get_tarski_option('nav_extlinkcat');
		$extlinks = get_bookmarks("category=$extlinks_cat");
		$target = $rel = '';
		$title  = '';
		foreach($extlinks as $link) {
			if($link->link_rel) {
				$rel = 'rel="' . $link->link_rel . '" ';
			}
			if($link->link_target) {
				$target = 'target="' . $link->link_target . '" ';
			}
			if($link->link_description) {
				$title = 'title="'. $link->link_description . '" ';
			}
			$navbar[] = sprintf(
				'<li><a id="nav-link-%1$s" %2$s href="%3$s">%4$s</a></li>',
				$link->link_id,
				$rel . $target . $title,
				$link->link_url,
				$link->link_name
			);
		}
	}
	
	return $navbar;
}

/**
 * Adds a WordPress dashboard link to the Tarski navbar.
 *
 * @since 2.0
 * @see tarski_navbar()
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
 * wrap_navlist() - Wraps the Tarski navbar in an unordered list element.
 * 
 * Unlike other navbar filters, wrap_navlist() doesn't make $navbar an array
 * if it isn't one, since that would result in it outputting an empty
 * unordered list. Instead, it simply returns false.
 * @since 2.0
 * @see tarski_navbar()
 * @param string $navbar
 * @return string $navbar
 */
function wrap_navlist($navbar) {
	if (is_array($navbar)) {
		array_unshift($navbar, '<ul class="primary xoxo">');
		array_push($navbar, '</ul>');
		return $navbar;
	} else {
		return false;
	}
}

/**
 * tarski_feedlink() - Adds the site feed link to the site navigation.
 * 
 * @since 2.0
 * @param boolean $return echo or return?
 * @return string $output
 */
function tarski_feedlink() {
	include(TARSKIDISPLAY . '/feed_link.php');
}

/**
 * tarski_bodyclass() - Returns the classes that should be applied to the document body.
 * 
 * @since 1.2
 * @param boolean $return
 * @return string $classes
 * @hook filter tarski_bodyclass
 * Filter the classes applied to the document body by Tarski.
 */
function tarski_bodyclass($return = false) {
	if(get_tarski_option('centred_theme')) { // Centred or not
		$classes[] = 'centre';
	}
	if(get_tarski_option('swap_sides')) { // Swapped or not
		$classes[] = 'janus';
	}
	if(get_tarski_option('style')) { // Alternate style
		$stylefile = get_tarski_option('style');
		$stylename = str_replace('.css', '', $stylefile);
		if(is_valid_tarski_style($stylefile)) {
			$classes[] = $stylename;
		}
	}
	if(get_bloginfo('text_direction') == 'rtl') {
		$classes[] = 'rtl';
	}
	
	// Filters should return an array
	$classes = apply_filters('tarski_bodyclass', $classes);
	
	// But if they don't, it won't implode
	if(is_array($classes))
		$classes = implode(' ', $classes);
	
	if($return)
		return $classes;
	else
		echo $classes;
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
 * tarski_searchform() - Outputs the WordPress search form.
 * 
 * Will only output the search form on pages that aren't a search
 * page or a 404, as these pages include the search form earlier
 * in the document and the search form relies on the 's' id value,
 * which as an HTML id must be unique within the document.
 * @since 2.0
 */
function tarski_searchform() {
	include_once(TEMPLATEPATH . "/searchform.php");
}

/**
 * tarski_credits() - Outputs the site feed and Tarski credits.
 * 
 * @since 1.5
 */
function tarski_credits() {
	if(detectWPMU())
		$current_site = get_current_site();
	
	include(TARSKIDISPLAY . "/credits.php");
}

?>