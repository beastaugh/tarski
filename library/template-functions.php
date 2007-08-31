<?php // template-functions.php - Templating functions for Tarski

// Header image status output
function tarski_header_status() {
	if(get_tarski_option('header') == 'blank.gif') {
		echo 'noheaderimage';
	} else {
		echo 'headerimage';
	}
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
		if(!get_tarski_option('display_title')) {
			$header_img_alt = get_bloginfo('name');
		} else {
			$header_img_alt = __('Header image','tarski');
		}
		
		if(get_theme_mod('header_image')) {
			$header_img_tag = '<img alt="'. $header_img_alt. '" src="'. get_header_image(). '" />'."\n";
		} else {
			$header_img_tag = '<img alt="'. $header_img_alt. '" src="'. $header_img_url. '" />'."\n";
		}

		echo '<div id="header-image">' . "\n";
		if(!get_tarski_option('display_title') && !is_home()) {
			echo '<a title="'. __('Return to front page','tarski'). '" href="'. get_bloginfo('home'). '">'. $header_img_tag. '</a>'."\n";
		} else {
			echo $header_img_tag;
		}		
		echo "</div>\n";
	}
}

// Site title output
function tarski_doctitle() {
	global $wp_query;
	$titleSep = '&middot;';

	echo get_bloginfo('name');

	if((get_option('show_on_front') == 'posts') && is_home()) {
		if(get_bloginfo('description') != '') {
			echo ' ' . $titleSep . ' ' . get_bloginfo('description');
		}
	} elseif(is_search()) {
		echo ' ' . $titleSep . ' ' . __('Search results','tarski');
	} elseif(is_month()) {
		echo ' ' . $titleSep . ' '; single_month_title(' ');
	} else {
		wp_title($titleSep);
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
			$prefix = '<p id="blog-title"><a title="' . __('Return to front page','tarski') . '" href="' . get_settings('home') . '/" rel="home">';
			$suffix = '</a></p>';
		}
	
		return $prefix . get_bloginfo('name') . $suffix;
	}
}

// Returns tagline in markup
function tarski_tagline() {
	if((get_tarski_option('display_tagline') && get_bloginfo('description') != '')) {
		return '<p id="tagline">' .  get_bloginfo('description') . '</p>';
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
function tarski_navbar() {
	$current = 'class="nav-current" ';
	if(get_option('show_on_front') != 'page') {
		if(is_home()) { $homeStatus = $current; }
		echo '<li><a id="nav-home" ' . $homeStatus . 'href="' . get_settings('home') . '/" rel="home">' . home_link_name() . "</a></li>\n";
	}
	
	global $wpdb;
	$nav_pages = get_tarski_option('nav_pages');
	if($nav_pages) {
		$nav_pages = explode(',', $nav_pages);
		foreach($nav_pages as $page) {
			if(is_page($page) || ((get_option('show_on_front') == 'page') && (get_option('page_for_posts') == $page) && is_home())) {
				$pageStatus = $current;
			} else {
				$pageStatus = '';
			}
			echo '			<li><a id="nav-' . $page . '-' . $wpdb->get_var("SELECT post_name from $wpdb->posts WHERE ID = $page") . '" ' . $pageStatus . 'href="' . get_permalink($page) . '">' . $wpdb->get_var("SELECT post_title from $wpdb->posts WHERE ID = $page") . '</a></li>' . "\n";
		}
	}
	global $navbarInclude;
	if($navbarInclude) {
		echo $navbarInclude . "\n";
	}
	if(is_user_logged_in()) {
		echo '<li><a id="nav-admin" href="' . get_option('siteurl') . '/wp-admin/">' . __('Site Admin','tarski') . '</a></li>' . "\n";
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
	function tarski_bodyid() {
		global $post, $wp_query;
	
		if(is_home()) {
			return 'home';
		} elseif(is_search()) {
			return 'search';
		} elseif(is_page()) {
			return 'page-'. $post->slug;
		} elseif(is_single()) {
			return 'post-'. $post->slug;
		} elseif(is_category()) {
			$cat_ID = get_query_var('cat');
			$cat_ID = (int) $cat_ID;
			$category = &get_category($cat_ID);
			$cat_slug = apply_filters('single_cat_title', $category->category_nicename);
			return 'cat-'. $cat_slug;
		} elseif(function_exists('is_tag')) {
			if(is_tag()) {
				$tag_ID = get_query_var('tag');
				$tag_ID = (int) $tag_ID;
				$tag = &get_term($tag_ID);
				$tag_slug = apply_filters('single_tag_title', $tag->category_nicename);
				return 'tag-'. $tag_slug;
			}
		} elseif(is_author()) {
			$author = the_archive_author();
			$author_slug = $author->user_login;
			return 'author-'. $author_slug;
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
		} elseif(is_404()) {
			return '404';
		} else {
			return 'unknown';
		}
	}
}

// Footer sidebar
function tarski_searchform() {
	if(!is_search()) {
		include(TEMPLATEPATH . '/searchform.php');
	}
}

// Default footer stuff including credit
function tarski_feed_and_credit() { ?>
	<div class="primary content">
		<p><?php if(detectWPMU()) { $current_site = get_current_site(); } _e('Powered by <a href="http://wordpress.org/">WordPress</a> and <a href="http://tarskitheme.com/">Tarski</a>', 'tarski'); ?><?php if(detectWPMU()) { echo ' | ' . __('Hosted by ', 'tarski') . '<a href="http://' . $current_site->domain . $current_site->path . '">' . $current_site->site_name . '</a>'; } ?></p>
	</div>
	<div class="secondary">
		<p><a class="feed" href="<?php echo get_bloginfo_rss('rss2_url'); ?>"><?php _e('Subscribe to feed', 'tarski'); ?></a></p>
	</div>
<?php }

// ~fin~ ?>