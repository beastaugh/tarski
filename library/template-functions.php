<?php // template-functions.php - Templating functions for Tarski

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

// Site title output
function get_tarski_doctitle() {
	global $wp_query;
	$sep = ' &middot; ';

	if((get_option('show_on_front') == 'posts') && is_home()) {
		if(get_bloginfo('description')) {
			$content = $sep. get_bloginfo('description');
		}
	} elseif(is_search()) {
		$content = $sep. __('Search results','tarski');
	} elseif(is_month()) {
		$content = $sep. single_month_title(' ', false);
	} else {
		$content = wp_title($sep, false);
	}
	
	return get_bloginfo('name'). $content;
}

function tarski_doctitle() {
	echo get_tarski_doctitle();
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
	$feed = '<p><a class="feed" href="'. get_bloginfo_rss('rss2_url'). '">'. __('Subscribe to feed', 'tarski'). '</a></p>'."\n";
	$suffix = '</div>'."\n";
	
	$output = $prefix. $feed. $suffix;
	if($return) {
		return $output;
	} else {
		echo $output;
	}
}

// Body classes
if(!function_exists('tarski_bodyclass')) {
	function tarski_bodyclass() {
		if(get_tarski_option('centered_theme')) { // Centred or not
			echo 'center';
		} else {
			echo 'left';
		}
		if(get_tarski_option('swap_sides')) { // Swapped or not
			echo ' janus';
		}
		if(get_tarski_option('style')) { // Alternate style
			echo ' ' . str_replace('.css', '', get_tarski_option('style'));
		}
		if (is_page() || is_single() || is_404()) { // Is it a single page?
			echo ' single';
		}
	}
}

// Body ids
if(!function_exists('tarski_bodyid')) {
	function get_tarski_bodyid() {
		global $post, $wp_query;
	
		if(is_home()) {
			return 'home';
		} elseif(is_search()) {
			return 'search';
		} elseif(is_page()) {
			return 'page-'. $post->post_name;
		} elseif(is_single()) {
			return 'post-'. $post->post_name;
		} elseif(is_category()) {
			$cat_ID = intval(get_query_var('cat'));
			$category = &get_category($cat_ID);
			return 'cat-'. $category->category_nicename;
		} elseif(is_author()) {
			$author = the_archive_author();
			return 'author-'. $author->user_login;
		} elseif(is_date()) {
			$year = get_query_var('year');
			$monthnum = get_query_var('monthnum');
			$day = get_query_var('day');
			if(is_year()) {
				return 'date-'. $year;
			} elseif(is_month()) {
				return 'date-'. $year. '-'. $monthnum;
			} elseif(is_day()) {
				return 'date-'. $year. '-'. $monthnum. '-'. $day;
			}
		} elseif(is_archive() && function_exists('is_tag')) {
			if(is_tag()) {
				$tag_ID = intval(get_query_var('tag_id'));
				$tag = &get_term($tag_ID, 'post_tag');
				return 'tag-'. $tag->slug;
			}	
		} elseif(is_404()) {
			return '404';
		} else {
			return 'unknown';
		}
	}
	
	function tarski_bodyid() {
		echo get_tarski_bodyid();
	}
}

// Footer sidebar
function tarski_searchform() {
	if(!is_search()) {
		include(TEMPLATEPATH . '/searchform.php');
	}
}

// Default footer stuff including credit
function tarski_feed_and_credit() {
	if(detectWPMU()) {
		$current_site = get_current_site();
	} ?>
	<div class="primary content">
		<p><?php _e('Powered by <a href="http://wordpress.org/">WordPress</a> and <a href="http://tarskitheme.com/">Tarski</a>', 'tarski');
		if(detectWPMU()) {
			echo ' | '. __('Hosted by ','tarski'). '<a href="http://'. $current_site->domain. $current_site->path. '">'. $current_site->site_name. '</a>';
		} ?></p>
	</div>
	<div class="secondary">
		<p><a class="feed" href="<?php echo get_bloginfo_rss('rss2_url'); ?>"><?php _e('Subscribe to feed', 'tarski'); ?></a></p>
	</div>
<?php }

// ~fin~ ?>