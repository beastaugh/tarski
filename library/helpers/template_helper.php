<?php

/**
 * is_wp_front_page() - Returns true when current page is the WP front page.
 * 
 * Very useful, since is_home() doesn't return true for the
 * front page if it's displaying a static page rather than
 * the usual posts page.
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
 * tarski_doctitle() - Returns or echoes the document title.
 * 
 * The order (site name first or last) can be set on the
 * Tarski Options page.
 * @param string $sep
 * @param boolean $swap title first or last?
 * @param boolean $return return or echo?
 * @return string $doctitle
 */
function tarski_doctitle($sep = "&middot;", $swap = false, $return = false) {
	$site_name = get_bloginfo("name");
	
	if(is_404()) {
		$content = __(sprintf('Error %s','404'),'tarski');
	} elseif((get_option("show_on_front") == "posts") && is_home()) {
		if(get_bloginfo("description")) {
			$content = get_bloginfo("description");
		}
	} elseif(is_search()) {
		$content = __("Search results","tarski");
	} elseif(is_month()) {
		$content = single_month_title(" ", false);
	} elseif(is_tag()) {
		$content = multiple_tag_titles(true);
	} else {
		$content = trim(wp_title("", false));
	}
	
	if($content) {
		$elements = array($site_name, $sep, $content);
		if((get_tarski_option("swap_title_order")) || $swap) {
			krsort($elements);
		}
		$doctitle = implode(" ", $elements);
	} else {
		$doctitle = $site_name;
	}
	
	$doctitle = apply_filters("tarski_doctitle", $doctitle);
	if($return) {
		return $doctitle;
	} else {
		echo $doctitle;
	}
}

/**
 * add_robots_meta() - Adds robots meta element if blog is public.
 * 
 * @since 2.0
 * @return string
 */
function add_robots_meta() {
	if(get_option('blog_public') != '0')
		echo '<meta name="robots" content="all" />'."\n";
}

/**
 * get_category_feed_link() - Gets the feed link for a given category.
 * 
 * Can be set to return Atom, RSS or RSS2. Currently being considered
 * for core inclusion, hence the conditional definiton.
 * @link http://trac.wordpress.org/ticket/5173
 * @since 2.0
 * @param integer $cat_id
 * @param string $feed
 * @return string $link
 */
if(!function_exists('get_category_feed_link')) {
	function get_category_feed_link($cat_id, $feed = 'rss2') {
		$cat_id = (int) $cat_id;

		$category = get_category($cat_id);

		if ( empty($category) || is_wp_error($category) )
			return false;

		$permalink_structure = get_option('permalink_structure');

		if ( '' == $permalink_structure ) {
			$link = get_option('home') . "?feed=$feed&amp;cat=" . $cat_id;
		} else {
			$link = get_category_link($cat_id);
			if( 'rss2' == $feed )
				$feed_link = 'feed';
			else
				$feed_link = "feed/$feed";

			$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
		}

		$link = apply_filters('category_feed_link', $link, $feed);

		return $link;
	}
}

/**
 * tarski_feeds() - Outputs feed links for the page.
 * 
 * Can be set to return Atom, RSS or RSS2. Will always return
 * the main site feed, but will additionally return an archive,
 * search or comments feed depending on the page type.
 * @since 2.0
 * @param boolean $return return or echo?
 * @global object $post
 * @global integer $id
 * @global object $authordata
 * @return string $feeds
 */
function tarski_feeds($return = false) {
	if(get_tarski_option("feed_type") == "atom")
		$type = "atom";
	else
		$type = "rss2";
	
	if(is_single() || (is_page() && ($comments || comments_open()))) {
		global $post;
		$title = sprintf( __('Commments feed for %s','tarski'), get_the_title() );
		$link = get_post_comments_feed_link($post->ID, $type);
	} elseif(is_archive()) {
		if(is_category()) {
			$title = sprintf( __('Category feed for %s','tarski'), single_cat_title('','',false) );
			$link = get_category_feed_link( get_query_var('cat'), $type );
		} elseif(is_tag()) {
			$title = sprintf( __('Tag feed for %s','tarski'), single_tag_title('','',false));
			$link = get_tag_feed_link(get_query_var('tag_id'), $type);
		} elseif(is_author()) {
			$title = sprintf( __('Articles feed for %s','tarski'), the_archive_author_displayname());
			$link = get_author_feed_link(get_query_var('author'), $type);
		} elseif(is_date()) {
			if(is_day()) {
				$title = sprintf( __('Daily archive feed for %s','tarski'), tarski_date());
				$link = get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d'));
			} elseif(is_month()) {
				$title = sprintf( __('Monthly archive feed for %s','tarski'), get_the_time('F Y'));
				$link = get_month_link(get_the_time('Y'), get_the_time('m'));
			} elseif(is_year()) {
				$title = sprintf( __('Yearly archive feed for %s','tarski'), get_the_time('Y'));
				$link = get_year_link(get_the_time('Y'));
			}
			if(get_settings('permalink_structure')) {
				$link .= "feed/";
				if($type == "atom") {
					$link .= "atom/";
				}
			} else {
				$link .= "&amp;feed=$type";
			}
		}
	} elseif(is_search()) {
		$title = sprintf( __('Search feed for %s','tarski'), attribute_escape(get_search_query()));
		$link = get_bloginfo('url'). '/?s='. attribute_escape(get_search_query()). "&amp;feed=$type";
	}
	$feed_link_type = "application/$type+xml";
	if($title && $link) {
		$feeds = sprintf(
			'<link rel="alternate" type="%1$s" title="%2$s" href="%3$s" />'."\n",
			$feed_link_type,
			$title,
			$link
		);
	}
	$feed_type_url = $type . "_url";
	$feeds .= sprintf(
		'<link rel="alternate" type="%1$s" title="%2$s" href="%3$s" />'."\n",
		$feed_link_type,
		get_bloginfo('name') . __(' feed','tarski'),
		get_bloginfo($feed_type_url)
	);
	$feeds = apply_filters('tarski_feeds', $feeds);
	
	if($return)
		return $feeds;
	else
		echo $feeds;
}

/**
 * tarski_headerimage() - Outputs header image.
 * 
 * @since 1.0
 * @return string
 */
function tarski_headerimage() {
	if($_SERVER['HTTP_HOST'] == 'themes.wordpress.net') { // Theme preview hack
		$header_img_url = 'http://tarskitheme.com/headers/greytree.jpg';
	} else {
		if(get_theme_mod('header_image')) {
			$header_img_url = get_header_image();
		} elseif(get_tarski_option('header')) {
			if(get_tarski_option('header') != 'blank.gif') {
				$header_img_url = get_bloginfo('template_directory') . '/headers/' . get_tarski_option('header');
			}
		} else {
			$header_img_url = get_bloginfo('template_directory') . '/headers/greytree.jpg';
		}
	}
	
	if($header_img_url) {
		if(get_tarski_option('display_title')) {
			$header_img_alt = __('Header image','tarski');		
		} else {
			$header_img_alt = get_bloginfo('name');
		}

		$header_img_tag = sprintf(
			'<img alt="%1$s" src="%2$s" />',
			$header_img_alt,
			$header_img_url
		);

		if(!get_tarski_option('display_title') && !is_home()) {
			$header_img_tag = sprintf(
				'<a title="%1$s" rel="home" href="%2$s">%3$s</a>',
				__('Return to front page','tarski'),
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
 */
function tarski_sitetitle() {
	if(get_tarski_option('display_title')) {
		$site_title = get_bloginfo('name');
		
		if(!is_wp_front_page()) {
			$site_title = sprintf(
				'<a title="%1$s" href="%2$s" rel="home">%3$s</a>',
				__('Return to front page','tarski'),
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
 */
function tarski_tagline() {
	if((get_tarski_option('display_tagline') && get_bloginfo('description')))
		$tagline = '<p id="tagline">'.  get_bloginfo('description'). '</p>';
	
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
 * @param boolean $return
 * @global object $wpdb
 * @return string $navbar
 */
function tarski_navbar($return = false) {
	global $wpdb;
	$current = 'class="nav-current" ';
	
	if(get_option('show_on_front') != 'page') {
		if(is_home()) {
			$home_status = $current;
		}
		$navbar = sprintf(
			'<li><a id="nav-home" '.'%1$s'.'href="%2$s" rel="home">%3$s</a></li>'."\n",
			$home_status,
			user_trailingslashit(get_bloginfo('url')),
			home_link_name()
		);
	}
	
	$nav_pages = get_tarski_option('nav_pages');
	if($nav_pages) {
		$nav_pages = explode(',', $nav_pages);
		foreach($nav_pages as $page) {
			if(is_page($page) || ((get_option('show_on_front') == 'page') && (get_option('page_for_posts') == $page) && is_home())) {
				$page_status = $current;
			} else {
				$page_status = false;
			}
						
			$navbar .= sprintf(
				'<li><a id="nav-%1$s" '.'%2$s'. 'href="%3$s">%4$s</a></li>'."\n",
				$page.'-'.$wpdb->get_var("SELECT post_name from $wpdb->posts WHERE ID = $page"),
				$page_status,
				get_permalink($page),
				$wpdb->get_var("SELECT post_title from $wpdb->posts WHERE ID = $page")
			);
		}
	}
	
	$navbar = apply_filters('tarski_navbar', $navbar);
	if($return) {
		return $navbar;
	} else {
		echo $navbar;
	}
}

/**
 * add_external_links() - Adds external links to the Tarski navbar.
 * 
 * @see get_tarski_navbar()
 * @param string $navbar
 * @return string $navbar
 */
function add_external_links($navbar) {
	if(get_tarski_option('nav_extlinkcat')) {
		$extlinks_cat = get_tarski_option('nav_extlinkcat');
		$extlinks = get_bookmarks("category=$extlinks_cat");
		foreach($extlinks as $link) {
			if($link->link_rel) {
				$rel = 'rel="'. $link->link_rel. '" ';
			}
			if($link->link_target) {
				$target = 'target="'. $link->link_target. '" ';
			}
			if($link->link_description) {
				$title = 'title="'. $link->link_description. '" ';
			}
			$navbar .= sprintf(
				'<li><a id="nav-link-%1$s" %2$s href="%3$s">%4$s</a></li>'."\n",
				$link->link_id,
				$rel. $target. $title,
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
 * @see get_tarski_navbar()
 * @param string $navbar
 * @return string $navbar
 */
function add_admin_link($navbar) {
	if(is_user_logged_in())
		$navbar .= '<li><a id="nav-admin" href="'. get_option('siteurl'). '/wp-admin/">'. __('Site Admin','tarski'). '</a></li>'. "\n";
	
	return $navbar;
}

/**
 * wrap_navlist() - Wraps the Tarski navbar in an unordered list element.
 * 
 * @see get_tarski_navbar()
 * @param string $navbar
 * @return string $navbar
 */
function wrap_navlist($navbar) {
	$navbar = '<ul class="primary xoxo">'."\n".$navbar.'</ul>'."\n";
	return $navbar;
}

/**
 * tarski_feedlink() - Adds the site feed link to the site navigation.
 * 
 * @param boolean $return echo or return?
 * @return string $output
 */
function tarski_feedlink() {
	if(get_tarski_option("feed_type") == "atom")
		$feed_url = "atom_url";
	else
		$feed_url = "rss2_url";
	
	include(TARSKIDISPLAY . "/feed_link.php");
}

/**
 * tarski_bodyclass() - Returns the classes that should be applied to the document body.
 * 
 * @param boolean $return
 * @return string $body_class
 */
function tarski_bodyclass($return = false) {
	$classes = array();
	
	if(get_tarski_option("centered_theme")) { // Centred or not
		array_push($classes, "centre");
	} else {
		array_push($classes, "left");
	}
	if(get_tarski_option("swap_sides")) { // Swapped or not
		array_push($classes, "janus");
	}
	if(get_tarski_option("style")) { // Alternate style
		$stylefile = get_tarski_option("style");
		$stylename = str_replace(".css", "", $stylefile);
		if(is_valid_tarski_style($stylefile)) {
			array_push($classes, $stylename);
		}
	}
	if (is_page() || is_single() || is_404()) { // Is it a single page?
		array_push($classes, "single");
	}
	if(get_bloginfo("text_direction") == "rtl") {
		array_push($classes, "rtl");
	}
	
	$body_classes = implode(" ", $classes);
	$body_classes = apply_filters("tarski_bodyclass", $body_classes);
	
	if($return)
		return $body_classes;
	else
		echo $body_classes;
}

/**
 * tarski_bodyid() - Outputs the id that should be applied to the document body.
 * 
 * @param boolean $return
 * @global object $post
 * @global object $wp_query
 * @return string $body_id
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
 */
function tarski_searchform() {
	include_once(TEMPLATEPATH . "/searchform.php");
}

/**
 * tarski_credits() - Outputs the site feed and Tarski credits.
 */
function tarski_credits() {
	if(detectWPMU())
		$current_site = get_current_site();
	
	include(TARSKIDISPLAY . "/credits.php");
}

?>