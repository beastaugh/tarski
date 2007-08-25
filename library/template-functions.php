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
		$headerImage = 'http://tarskitheme.com/wp-content/themes/tarski/headers/greytree.jpg';
	} else {
		if(get_tarski_option('header')) {
			if(get_tarski_option('header') != 'blank.gif') {
				$headerImage = get_bloginfo('template_directory') . '/headers/' . get_tarski_option('header');
			}
		} else {
			$headerImage = get_bloginfo('template_directory') . '/headers/greytree.jpg';
		}
	}
	
	if($headerImage) {
		echo '<div id="header-image">' . "\n";
		if(get_theme_mod('header_image')) {
			echo '	<img alt="' . __('Header image','tarski') . '" src="';
			header_image();
			echo '" />' . "\n";
		} else {
			echo '	<img alt="' . __('Header image','tarski') . '" src="' . $headerImage . '" />' . "\n";
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
	$frontPageID = get_option('page_on_front');
	
	if(get_tarski_option('display_title') != 'lolno') {
		if((get_option('show_on_front') == 'page') && ($frontPageID == $wp_query->post->ID)) {
			$prefix = '<p id="blog-title">';
			$suffix = '</p>';
		} elseif((get_option('show_on_front') == 'posts') && is_home()) {
			$prefix = '<h1 id="blog-title">';
			$suffix = '</h1>';
		} else {
			$prefix = '<p id="blog-title"><a title="' . __('Return to front page','tarski') . '" href="' . get_settings('home') . '" rel="home">';
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
	$openingTag = '<div id="title">';
	$closingTag = '</div>';
	
	if(tarski_tagline() || tarski_sitetitle()) {
		echo $openingTag . "\n";
		echo tarski_sitetitle() . "\n";
		echo tarski_tagline() . "\n";
		echo $closingTag . "\n";
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
		echo '<li><a id="nav-home" ' . $homeStatus . 'href="' . get_settings('home') . '" rel="home">' . home_link_name() . "</a></li>\n";
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

// Footer sidebar
function tarski_searchform() {
	if(!is_search()) {
		include(TEMPLATEPATH . '/searchform.php');
	}
}

function tarski_livecomments_integration() {
	if (function_exists('live_preview')) {
		live_preview();
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