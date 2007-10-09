<?php

// Site title output
function tarski_doctitle($sep = "&middot;", $swap = false, $return = false) {
	global $wp_query;
	$site_name = get_bloginfo("name");

	if((get_option("show_on_front") == "posts") && is_home()) {
		if(get_bloginfo("description")) {
			$content = get_bloginfo("description");
		}
	} elseif(is_search()) {
		$content = __("Search results","tarski");
	} elseif(is_month()) {
		$content = single_month_title(" ", false);
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

function add_robots_meta() {
	if(get_option('blog_public') != '0') {
		echo '<meta name="robots" content="all" />'."\n";
	}
}

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

			$link = $link . user_trailingslashit($feed_link, 'feed');
		}

		$link = apply_filters('category_feed_link', $link, $feed);

		return $link;
	}
}

if(!function_exists('get_author_feed_link')) {
	function get_author_feed_link($echo = false, $author_id, $author_nicename, $type = "rss2") {
		$auth_ID = (int) $author_id;
		$permalink_structure = get_option('permalink_structure');

		if ( '' == $permalink_structure ) {
			$link = get_option('home') . "?feed=$type&amp;author=" . $author_id;
		} else {
			$link = get_author_posts_url($author_id, $author_nicename);
			$link = trailingslashit($link);
			if( $type != "rss2" ) {
				 $link .= user_trailingslashit("feed/$type", 'feed');
			} else {
				$link .= user_trailingslashit('feed', 'feed');
			}
		}

		$link = apply_filters('author_feed_link', $link);

		if ( $echo )
			echo $link;
		return $link;
	}
}


function tarski_feeds($return = false) {
	if(get_tarski_option("feed_type") == "atom") {
		$type = "atom";
	} else {
		$type = "rss2";
	}
	if(is_single() || (is_page() && ($comments || comments_open()))) {
		global $post;
		$title = sprintf( __('Commments feed for %s','tarski'), get_the_title() );
		if(function_exists('get_post_comments_feed_link')) {
			$link = get_post_comments_feed_link($post->ID, $type);
		} elseif(function_exists('comments_rss')) {
			$id = $post->ID;
			global $id;
			$link = comments_rss();
		}
	} elseif(is_archive()) {
		if(is_category()) {
			$title = sprintf( __('Category feed for %s','tarski'), single_cat_title('','',false) );
			$link = get_category_feed_link( get_query_var('cat'), $type );
		} elseif(is_author()) {
			global $authordata;
			$title = sprintf( __('Articles feed for %s','tarski'), the_archive_author_displayname());
			$link = get_author_feed_link(false, get_query_var('author'), $authordata->user_nicename, $type);
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
		} elseif(function_exists('is_tag')) {
			if(is_tag()) {
				$title = sprintf( __('Tag feed for %s','tarski'), single_tag_title('','',false));
				$link = get_tag_feed_link(get_query_var('tag_id'), $type);
			}
		}
	} elseif(is_search()) {
		$title = sprintf( __('Search feed for %s','tarski'), attribute_escape(get_search_query()));
		$link = get_bloginfo('url'). '/?s='. attribute_escape(get_search_query()). "&amp;feed=$type";
	}
	if(get_tarski_option('feed_type') == 'atom') {
		$feed_link_type = 'application/atom+xml';
	} else {
		$feed_link_type = 'application/rss+xml';
	}
	if($title && $link) {
		$feeds = sprintf(
			'<link rel="alternate" type="%1$s" title="%2$s" href="%3$s" />'."\n",
			$feed_link_type,
			$title,
			$link
		);
	}
	$feeds .= sprintf(
		'<link rel="alternate" type="%1$s" title="%2$s" href="%3$s" />'."\n",
		$feed_link_type,
		get_bloginfo('name'). __(' feed','tarski'),
		get_bloginfo('rss2_url')
	);
	$feeds = apply_filters('tarski_feeds', $feeds);
	
	if($return) {
		return $feeds;
	} else {
		echo $feeds;
	}
}

// Header image status output
function get_tarski_header_status() {
	if(get_tarski_option('header') == 'blank.gif') {
		return 'noheaderimage';
	} else {
		return 'headerimage';
	}
}

function tarski_header_status() {
	echo get_tarski_header_status();
}

// Header image output
function tarski_headerimage() {
	if($_SERVER['HTTP_HOST'] == 'themes.wordpress.net') { // Makes the theme preview work properly
		$header_img_url = 'http://tarskitheme.com/wp-content/themes/tarski/headers/greytree.jpg';
	} else {
		if(get_tarski_option('header')) {
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
		
		if(get_theme_mod('header_image')) {
			$header_img_tag = '<img alt="'. $header_img_alt. '" src="'. get_header_image(). '" />'."\n";
		} else {
			$header_img_tag = '<img alt="'. $header_img_alt. '" src="'. $header_img_url. '" />'."\n";
		}

		echo '<div id="header-image">' . "\n";
		if(!get_tarski_option('display_title') && !is_home()) {
			echo '<a title="'. __('Return to front page','tarski'). '" rel="home" href="'. get_bloginfo('url'). '/">'. $header_img_tag. '</a>'."\n";
		} else {
			echo $header_img_tag;
		}		
		echo "</div>\n";
	}
}

// Returns site title in markup
function tarski_sitetitle() {
	global $wp_query;
	$front_page_id = get_option('page_on_front');
	
	if(get_tarski_option('display_title')) {
		if((get_option('show_on_front') == 'page') && ($front_page_id == $wp_query->post->ID)) {
			$prefix = '<p id="blog-title">';
			$suffix = '</p>';
		} elseif((get_option('show_on_front') == 'posts') && is_home()) {
			$prefix = '<h1 id="blog-title">';
			$suffix = '</h1>';
		} else {
			$prefix = '<p id="blog-title"><a title="' . __('Return to front page','tarski') . '" href="' . get_bloginfo('url') . '/" rel="home">';
			$suffix = '</a></p>';
		}
	
		return $prefix . get_bloginfo('name') . $suffix;
	}
}

// Returns tagline in markup
function tarski_tagline() {
	if((get_tarski_option('display_tagline') && get_bloginfo('description'))) {
		return '<p id="tagline">'.  get_bloginfo('description'). '</p>';
	}
}

// Outputs site title and tagline
function tarski_titleandtag() {
	$opening_tag = '<div id="title">';
	$closing_tag = '</div>';
	
	if(tarski_tagline() || tarski_sitetitle()) {
		echo $opening_tag . "\n";
		echo tarski_sitetitle() . "\n";
		echo tarski_tagline() . "\n";
		echo $closing_tag . "\n";
	}
}

function home_link_name() {
	if(get_tarski_option('home_link_name')) {
		return get_tarski_option('home_link_name');
	} else {
		return __('Home','tarski');
	}
}

// Navbar
function get_tarski_navbar() {
	global $wpdb;
	$current = 'class="nav-current" ';
	
	if(get_option('show_on_front') != 'page') {
		if(is_home()) {
			$home_status = $current;
		}
		$output = sprintf(
			'<li><a id="nav-home" '.'%1$s'.'href="%2$s" rel="home">%3$s</a></li>'."\n",
			$home_status,
			get_bloginfo('url').'/',
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
						
			$output .= sprintf(
				'<li><a id="nav-%1$s" '.'%2$s'. 'href="%3$s">%4$s</a></li>'."\n",
				$page.'-'.$wpdb->get_var("SELECT post_name from $wpdb->posts WHERE ID = $page"),
				$page_status,
				get_permalink($page),
				$wpdb->get_var("SELECT post_title from $wpdb->posts WHERE ID = $page")
			);
		}
	}
	
	$output = apply_filters('tarski_navbar', $output);
	return $output;
}

function tarski_navbar() {
	echo get_tarski_navbar();
}

function add_external_links($input) {
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
			$input .= sprintf(
				'<li><a id="nav-link-%1$s" %2$s href="%3$s">%4$s</a></li>'."\n",
				$link->link_id,
				$rel. $target. $title,
				$link->link_url,
				$link->link_name
			);
		}
	}
	return $input;
}

function add_admin_link($input) {
	if(is_user_logged_in()) {
		$input .= '<li><a id="nav-admin" href="'. get_option('siteurl'). '/wp-admin/">'. __('Site Admin','tarski'). '</a></li>'. "\n";
	}
	return $input;
}

function wrap_navlist($input) {
	$input = '<ul class="primary xoxo">'."\n".$input.'</ul>'."\n";
	return $input;
}

function tarski_navbar_feedlink($return = false) {
	$prefix = '<div class="secondary">'."\n";
	if(get_tarski_option("feed_type") == "atom") {
		$feed_url = "atom_url";
	} else {
		$feed_url = "rss2_url";
	}
	$feed = '<p><a class="feed" href="'. get_bloginfo($feed_url). '">'. __('Subscribe to feed', 'tarski'). '</a></p>'."\n";
	$suffix = '</div>'."\n";
	
	$output = $prefix. $feed. $suffix;
	if($return) {
		return $output;
	} else {
		echo $output;
	}
}

// Body classes
function get_tarski_bodyclass() {
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
		$stylename = str_replace(".css", "", get_tarski_option("style"));
		array_push($classes, $stylename);
	}
	if (is_page() || is_single() || is_404()) { // Is it a single page?
		array_push($classes, "single");
	}
	
	$body_class = implode(" ", $classes);
	$body_class = apply_filters("tarski_bodyclass", $body_class);
	return $body_class;
}

function tarski_bodyclass() {
	echo get_tarski_bodyclass();
}

// Body ids
function get_tarski_bodyid() {
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
	} elseif(is_archive() && function_exists('is_tag')) {
		if(is_tag()) {
			$tag_ID = intval(get_query_var('tag_id'));
			$tag = &get_term($tag_ID, 'post_tag');
			$body_id = 'tag-'. $tag->slug;
		}	
	} elseif(is_404()) {
		$body_id = '404';
	} else {
		$body_id = 'unknown';
	}
	$body_id = apply_filters('tarski_bodyid', $body_id);
	return $body_id;
}	

function tarski_bodyid() {
	echo get_tarski_bodyid();
}

// Footer sidebar
function tarski_searchform() {
	if(!is_search() && !is_404()) {
		include(TEMPLATEPATH . "/searchform.php");
	}
}

// Default footer stuff including credit
function tarski_feed_and_credit() {
	if(get_tarski_option("feed_type") == "atom") {
		$feed_url = "atom_url";
	} else {
		$feed_url = "rss2_url";
	}
	if(detectWPMU()) {
		$current_site = get_current_site();
	}
	include(TARSKIDISPLAY . "/footer/feed_and_credit.php");
}

?>