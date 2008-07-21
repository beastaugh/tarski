<?php

/**
 * is_wp_front_page() - Returns true when current page is the WP front page.
 * 
 * Very useful, since is_home() doesn't return true for the front page
 * if it's displaying a static page rather than the usual posts page.
 * @since 2.0
 * @return boolean
 */
function is_wp_front_page() {
	if(get_option('show_on_front') == 'page')
		return is_page(get_option('page_on_front'));
	else
		return is_home();
}

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
	
	if(is_404()) {
		$content = __(sprintf('Error %s','404'),'tarski');
	} elseif((get_option('show_on_front') == 'posts') && is_home()) {
		if(get_bloginfo('description')) {
			$content = get_bloginfo('description');
		}
	} elseif(is_search()) {
		$content = sprintf( __('Search results for %s','tarski'), attribute_escape(get_search_query()) );
	} elseif(is_month()) {
		$content = single_month_title(' ', false);
	} elseif(is_tag()) {
		$content = multiple_tag_titles();
	} else {
		$content = trim(wp_title('', false));
	}
	
	if($content) {
		$elements = array(
			'site_name' => $site_name,
			'separator' => $sep,
			'content' => $content
		);
	} else {
		$elements = array(
			'site_name' => $site_name
		);
	}
	
	if(get_tarski_option('swap_title_order')) {
		$elements = array_reverse($elements, true);
	}
	
	// Filters should return an array
	$elements = apply_filters('tarski_doctitle', $elements);
	
	// But if they don't, it won't try to implode
	if(check_input($elements, 'array'))
		$doctitle = implode(' ', $elements);
	
	echo $doctitle;
}

/**
 * add_version_to_styles() - Adds version number to style links.
 * 
 * This makes browsers re-download the CSS file when the version
 * number changes, reducing problems that may occur when markup
 * changes but the corresponding new CSS is not downloaded.
 * @since 2.0.1
 * @see tarski_stylesheets()
 * @param array $style_array
 * @return array $style_array
 */
function add_version_to_styles($style_array) {
	if(check_input($style_array, 'array')) {
		foreach($style_array as $type => $values) {
			if(is_array($values) && $values['url']) {
				$style_array[$type]['url'] .= '?v=' . theme_version();
			}
		}
	}
	return $style_array;
}

/**
 * generate_feed_link() - Returns a properly formatted RSS or Atom feed link
 *
 * @since 2.1
 * @param string $title
 * @param string $link
 * @param string $type
 * @return string
 */
function generate_feed_link($title, $link, $type = '') {
	if ( $type == '' )
		$type = feed_link_type();
	
	return "<link rel=\"alternate\" type=\"$type\" title=\"$title\" href=\"$link\" />";
}

/**
 * feed_link_type() - Returns an Atom or RSS feed MIME type
 *
 * @since 2.1
 * @param string $type
 * @return string
 */
function feed_link_type($type = '') {
	if(empty($type))
		$type = get_default_feed();
	
	if($type == 'atom')
		return 'application/atom+xml';
	else
		return 'application/rss+xml';
}

/**
 * tarski_headerimage() - Outputs header image.
 * 
 * @since 1.0
 * @return string
 */
function tarski_headerimage() {
	if(get_theme_mod('header_image')) {
		$header_img_url = get_header_image();
	} elseif(get_tarski_option('header')) {
		if(get_tarski_option('header') != 'blank.gif') {
			$header_img_url = get_bloginfo('template_directory') . '/headers/' . get_tarski_option('header');
		}
	} else {
		$header_img_url = get_bloginfo('template_directory') . '/headers/greytree.jpg';
	}
	
	if($header_img_url) {
		if(get_tarski_option('display_title')) {
			$header_img_alt = __('Header image','tarski');		
		} else {
			$header_img_alt = get_bloginfo('name');
		}

		$header_img_tag = "<img alt=\"$header_img_alt\" src=\"$header_img_url\" />";

		if(!get_tarski_option('display_title') && !is_wp_front_page()) {
			$header_img_tag = sprintf(
				'<a title="%1$s" rel="home" href="%2$s">%3$s</a>',
				__('Return to main page','tarski'),
				user_trailingslashit(get_bloginfo('url')),
				$header_img_tag
			);
		}
		
		echo "<div id=\"header-image\">$header_img_tag</div>\n\n";
	}
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
		
		if(!is_wp_front_page()) {
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
	if((get_tarski_option('display_tagline') && get_bloginfo('description')))
		$tagline = '<p id="tagline">' .  get_bloginfo('description') . '</p>';
	
	$tagline = apply_filters('tarski_tagline', $tagline);
	return $tagline;
}

/**
 * tarski_titleandtag() - Outputs site title and tagline.
 * 
 * @since 1.5
 * @return string
 */
function tarski_titleandtag() {
	if(tarski_tagline() || tarski_sitetitle()) {
		echo '<div id="title">'."\n";
		echo tarski_sitetitle() . "\n";
		echo tarski_tagline() . "\n";
		echo '</div>'."\n";
	}
}

/**
 * navbar_wrapper() - Outputs navigation section.
 * 
 * @see th_navbar()
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
	if(get_tarski_option('home_link_name'))
		return get_tarski_option('home_link_name');
	else
		return __('Home','tarski');
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
	
	if(get_option('show_on_front') != 'page') {
		if(is_home()) {
			$home_status = $current;
		} else {
			$home_status = false;
		}
		$navbar['home'] = sprintf(
			'<li><a id="nav-home"%1$s href="%2$s" rel="home">%3$s</a></li>',
			$home_status,
			user_trailingslashit(get_bloginfo('url')),
			home_link_name()
		);
	}
	
	$pages = &get_pages('sort_column=post_parent,menu_order');
	$nav_pages = explode(',', get_tarski_option('nav_pages'));
	
	if(!empty($nav_pages) && !empty($pages)) {
		foreach($pages as $page) {
			if(in_array($page->ID, $nav_pages)) {
				if(is_page($page->ID) || ((get_option('show_on_front') == 'page') && (get_option('page_for_posts') == $page->ID) && is_home())) {
					$page_status = $current;
				} else {
					$page_status = false;
				}
				
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
	if(check_input($navbar, 'array') && !empty($navbar)) {
		$navbar = "\n" . implode("\n", $navbar) . "\n\n";
	} else {
		$navbar = false;
	}

	if($return) {
		return $navbar;
	} else {
		echo $navbar;
	}
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
	if(!check_input($navbar, 'array'))
		$navbar = array();
	
	if(get_tarski_option('nav_extlinkcat')) {
		$extlinks_cat = get_tarski_option('nav_extlinkcat');
		$extlinks = get_bookmarks("category=$extlinks_cat");
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
 * add_admin_link() - Adds a WordPress site admin link to the Tarski navbar.
 * 
 * @since 2.0
 * @see tarski_navbar()
 * @param string $navbar
 * @return string $navbar
 */
function add_admin_link($navbar) {
	if(!check_input($navbar, 'array'))
		$navbar = array();
	
	if(is_user_logged_in())
		$navbar['admin'] = sprintf(
			'<li><a id="nav-admin" href="%1$s">%2$s</a></li>',
			 admin_url(),
			__('Site Admin','tarski')
		);	
	
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
	if(check_input($navbar, 'array')) {
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