<?php // functions.php - Tarski functions library

global $tarski_options;
flush_tarski_options();

// Version detection
$themeData = get_theme_data(TEMPLATEPATH . '/style.css');
$installedVersion = trim($themeData['Version']);
if(!$installedVersion) {
	$installedVersion = "unknown";
}

// upgrade to serialised options, implemented in 1.4...
if(!get_option('tarski_options')) {
	$tarski_options = array();
	
	$tarski_options['sidebar_type'] = get_option('tarski_sidebar_type');
	$tarski_options['update_notification'] = get_option('tarski_update_notification');
	$tarski_options['header'] = get_option('tarski_header');
	$tarski_options['nav_pages'] = get_option('tarski_nav_pages');
	$tarski_options['centered_theme'] = get_option('tarski_centered_theme');
	$tarski_options['swap_sides'] = get_option('tarski_swap_sides');
	$tarski_options['style'] = get_option('tarski_style');
	$tarski_options['installed'] = get_option('tarski_installed');
	$tarski_options['blurb'] = get_option('blurb');
	$tarski_options['asidescategory'] = get_option('tarski_asidescategory');
	$tarski_options['about_text'] = get_option('about_text');
	$tarski_options['footer_recent'] = get_option('tarski_footer_recent');
	$tarski_options['sidebar_pages'] = get_option('tarski_sidebar_pages');
	$tarski_options['sidebar_links'] = get_option('tarski_sidebar_links');
	$tarski_options['sidebar_comments'] = get_option('tarski_sidebar_comments');
	$tarski_options['sidebar_custom'] = get_option('tarski_sidebar_custom');
	$tarski_options['sidebar_onlyhome'] = get_option('tarski_sidebar_onlyhome');
	$tarski_options['display_title'] = get_option('tarski_display_title');
	$tarski_options['display_tagline'] = get_option('tarski_display_tagline');
	$tarski_options['hide_categories'] = get_option('tarski_hide_categories');
	$tarski_options['use_pages'] = get_option('tarski_use_pages');
	$tarski_options['ajax_tags'] = get_option('tarski_ajax_tags');
	
	ksort($tarski_options);
	$tarski_options = serialize($tarski_options);
	update_option('tarski_options', $tarski_options);
	
	delete_option('tarski_sidebar_type');
	delete_option('tarski_update_notification');
	delete_option('tarski_header');
	delete_option('tarski_nav_pages');
	delete_option('tarski_centered_theme');
	delete_option('tarski_swap_sides');
	delete_option('tarski_style');
	delete_option('tarski_installed');
	delete_option('blurb');
	delete_option('tarski_asidescategory');
	delete_option('about_text');
	delete_option('tarski_footer_recent');
	delete_option('tarski_sidebar_pages');
	delete_option('tarski_sidebar_links');
	delete_option('tarski_sidebar_comments');
	delete_option('tarski_sidebar_custom');
	delete_option('tarski_sidebar_onlyhome');
	delete_option('tarski_display_title');
	delete_option('tarski_display_tagline');
	delete_option('tarski_hide_categories');
	delete_option('tarski_use_pages');
	delete_option('tarski_ajax_tags');
	
	// hopefully bypass errors?
	echo "<script>window.location.replace('${_SERVER['REQUEST_URI']}');</script>\n";
	exit;
}

// if no widgets, don't use the widgets sidebar
if(!function_exists('register_sidebar') && get_tarski_option('sidebar_type') == 'widgets') {
	update_tarski_option('sidebar_type', '');
}

// set default sidebar type
if(!get_tarski_option('sidebar_type')) {
	// default to widgets if available, otherwise use the Tarski sidebar
	if(function_exists('register_sidebar')) {
		update_tarski_option('sidebar_type', 'widgets');
	} else {
		update_tarski_option('sidebar_type', 'tarski');
	}
}

// Constants file include
@include(TEMPLATEPATH . '/constants.php');

// Localisation
load_theme_textdomain('tarski');

// Options page and dashboard injections
add_action('admin_head', 'tarski_inject_scripts');
add_action('activity_box_end', 'update_dashboard');

// Widgets
if(function_exists('register_sidebar')) {
	register_sidebar(array(
		'name' => __('Main Sidebar', 'tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => __('Footer Widgets', 'tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
}

// custom header API
if(function_exists('add_custom_image_header')) {
	define('HEADER_TEXTCOLOR', '');
	define('HEADER_IMAGE', '%s/headers/' . get_tarski_option('header')); // %s is theme dir uri
	define('HEADER_IMAGE_WIDTH', 720);
	define('HEADER_IMAGE_HEIGHT', 180);
	define('NO_HEADER_TEXT', true );
	
	function tarski_admin_header_style() { ?>
<style type="text/css">
#headimg {
	height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
}

#headimg h1, #headimg #desc {
	display: none;
}

</style>
<?php }
	
	add_custom_image_header('', 'tarski_admin_header_style');
}




// serialisation stuff

// update the options array from the database
function flush_tarski_options() {
	global $tarski_options;
	$tarski_options = unserialize(get_option('tarski_options'));
}

function add_tarski_option($name, $value) {
	update_tarski_option($name, $value);
}

// get a specific option
function get_tarski_option($name) {
	global $tarski_options;
	return $tarski_options[$name];
}

// update a specific option
function update_tarski_option($name, $value) {
	global $tarski_options;
	$tarski_options[$name] = $value;
	update_option('tarski_options', serialize($tarski_options));
	flush_tarski_options();
}

// for multiple option updates, can pass an array('name' => 'value'); with all options to save queries
function update_tarski_options($array) {
	global $tarski_options;
	foreach($array as $name => $value) {
		$tarski_options[$name] = $value;
	}
	update_option('tarski_options', serialize($tarski_options));
	flush_tarski_options();
}

// detect WordPress MultiUser - http://mu.wordpress.org/
function detectWPMU() {
	return function_exists('is_site_admin');
}


// Dashboard update notification
function update_dashboard() {
	global $installedVersion;
	
	if(!detectWPMU()) {
		echo "<h3>" . __("Tarski Updates", "tarski") . "</h3>\n";
		if(get_tarski_option('update_notification') == 'true') {
			echo "<script src=\"http://tarskitheme.com/version.php?version=$installedVersion&verbose=true\" type=\"text/javascript\"></script>\n";
		} else {
			echo "<p>" . __("Update notification for the Tarski theme ", "tarski") . "<a href=\"themes.php?page=functions.php\">" . __("is currently turned off", "tarski") . "</a>" . __(".", "tarski") . "</p>\n";
		}
	}
}

// Multiple user check
$count_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->usermeta WHERE `meta_key` = '" . $wpdb->prefix . "user_level' AND `meta_value` > 1");
	if ($count_users > 1) { $multipleAuthors = 1; }

// Clean page linkage
function link_pages_without_spaces() {
	ob_start();
	link_pages('<p class="pagelinks"><strong>Pages</strong>', '</p>', 'number', '', '', '%', '');
	$text = ob_get_contents();
	ob_end_clean();
	
	$text = str_replace(' <a href', '<a href', $text);
	$text = str_replace('> ', '>', $text);
	echo $text;
}

// Header image check
if(get_tarski_option('header') == 'blank.gif') {
	$noHeaderImage = true;
}

// Header image status output
function tarski_header_status() {
	global $noHeaderImage;
	if($noHeaderImage) {
		return 'noheaderimage';
	} else {
		return 'headerimage';
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
			echo '	<img alt="' . __('Header image', 'tarski') . '" src="';
			header_image();
			echo '" />' . "\n";
		} else {
			echo '	<img alt="' . __('Header image', 'tarski') . '" src="' . $headerImage . '" />' . "\n";
		}
		echo "</div>\n";
	}
}

// Site title output
function tarski_title($type = 'title') {
	$titleSep = '&middot;';
	// tarski_title('header') is for use within the document <body>
	if ($type == 'header') {
		if(is_home()) { $prefix = '<h1 id="blog-title">'; $suffix = '</h1>'; }
		else { $prefix = '<p id="blog-title"><a title="' . __('Return to front page', 'tarski') . '" href="' . get_settings('home') . '">'; $suffix = '</a></p>'; }
		echo $prefix . get_bloginfo('name') . $suffix . "\n";
	}
	// tarski_title() is for use within the document <title>
	else { echo get_bloginfo('name');
		if (is_home()) { if (get_bloginfo('description') != '') { echo ' ' . $titleSep . ' ' . get_bloginfo('description'); } }
		elseif (is_search()) { echo ' ' . $titleSep . ' Search results'; }
		elseif (is_month()) { echo ' ' . $titleSep . ' '; single_month_title(' '); }
		else { wp_title($titleSep); }
	}
}

// Navbar
function tarski_navbar() {
	$current = 'class="nav-current" ';
	if(is_home()) { $homeStatus = $current; }
	echo '<li><a id="nav-home" ' . $homeStatus . 'href="' . get_settings('home') . '">' . __('Home', 'tarski') . "</a></li>\n";
	
	global $wpdb;
	$nav_pages = get_tarski_option('nav_pages');
	if($nav_pages) {
		$nav_pages = explode(',', $nav_pages);
		foreach($nav_pages as $page) {
			if(is_page($page)) { $pageStatus = $current; } else { $pageStatus = ''; }
			echo '			<li><a id="nav-' . $page . '-' . $wpdb->get_var("SELECT post_name from $wpdb->posts WHERE ID = $page") . '" ' . $pageStatus . 'href="' . get_permalink($page) . '">' . $wpdb->get_var("SELECT post_title from $wpdb->posts WHERE ID = $page") . '</a></li>' . "\n";
		}
	}
	global $navbarInclude;
	if($navbarInclude) {
		echo $navbarInclude . "\n";
	}
	if(is_user_logged_in()) {
		echo '<li><a href="' . get_option('siteurl') . '/wp-admin/">' . __('Site Admin', 'tarski') . '</a></li>' . "\n";
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
	global $headerImageSet;
	if($headerImageSet == false) { // No header image
		echo ' noheader';
	}
}

// A better the_date() function
function tarski_date() {
	global $post;
	return mysql2date(get_settings('date_format'), $post->post_date);
}

// Tarski excerpts
// Code shamelessly borrowed from http://guff.szub.net/2005/02/26/the-excerpt-reloaded/
function tarski_excerpt($excerpt_length = 120, $allowedtags = '', $filter_type = 'none', $use_more_link = 1, $more_link_text = '(more...)', $force_more = 1, $fakeit = 1, $no_more = 0, $more_tag = 'div', $more_link_title = 'Continue reading this entry', $showdots = 1) {
	global $post;

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) { // and it doesn't match cookie
			if(is_feed()) { // if this runs in a feed
				$output = __('This entry is protected.','tarski');
			} else {
				$output = get_the_password_form();
			}
		}
		return $output;
	}

	if($fakeit == 2) { // force content as excerpt
		$text = $post->post_content;
	} elseif($fakeit == 1) { // content as excerpt, if no excerpt
		$text = (empty($post->post_excerpt)) ? $post->post_content : $post->post_excerpt;
	} else { // excerpt no matter what
		$text = $post->post_excerpt;
	}

	if($excerpt_length < 0) {
		$output = $text;
	} else {
	if(!$no_more && strpos($text, '<!--more-->')) {
		$text = explode('<!--more-->', $text, 2);
			$l = count($text[0]);
			$more_link = 1;
		} else {
			$text = explode(' ', $text);
			if(count($text) > $excerpt_length) {
				$l = $excerpt_length;
				$ellipsis = 1;
			} else {
				$l = count($text);
				$more_link_text = '';
				$ellipsis = 0;
			}
		}
		for ($i=0; $i<$l; $i++)
			$output .= $text[$i] . ' ';
	}

	if('all' != $allowed_tags) {
		$output = strip_tags($output, $allowedtags);
	}

	$output = rtrim($output, "\s\n\t\r\0\x0B");
	$output = ($fix_tags) ? $output : balanceTags($output);
	$output .= ($showdots && $ellipsis) ? '...' : '';

	switch($more_tag) {
		case('div') :
			$tag = 'div';
			break;
		case('span') :
			$tag = 'span';
			break;
		case('p') :
			$tag = 'p';
			break;
		default :
			$tag = 'span';
			break;
	}

	if ($use_more_link && $more_link_text) {
		if($force_more) {
			$output .= ' <' . $tag . ' class="more-link"><a href="'. get_permalink($post->ID) . '#more-' . $post->ID .'" title="' . $more_link_title . '">' . $more_link_text . '</a></' . $tag . '>' . "\n";
		} else {
			$output .= ' <' . $tag . ' class="more-link"><a href="'. get_permalink($post->ID) . '" title="' . $more_link_title . '">' . $more_link_text . '</a></' . $tag . '>' . "\n";
		}
	}

	$output = apply_filters($filter_type, $output);
	return $output;
}

// Options page JS and CSS injection
function tarski_inject_scripts() {
	if(substr($_SERVER['REQUEST_URI'], -39, 39) == 'wp-admin/themes.php?page=tarski-options') { // Ugly
		echo "\n\n";
		echo '<script src="' . get_bloginfo('wpurl') . '/wp-includes/js/prototype.js' . '" type="text/javascript"></script>' . "\n";
		echo '<script src="' . get_bloginfo('wpurl') . '/wp-includes/js/scriptaculous/scriptaculous.js' .'" type="text/javascript"></script>' . "\n";
		// Empty JavaScript file we might have a use for in the future
		// echo '<script src="' . get_bloginfo('template_directory') . '/js/tarski.js' .'" type="text/javascript"></script>' . "\n";
		echo '<link rel="stylesheet" href="' . get_bloginfo('template_directory') . '/library/options.css" type="text/css" media="screen" />' . "\n";
		echo "\n";
	}
}

// Update function
// I r serious cat. This r serious function.
function tarskiupdate() {
	global $wpdb, $user_ID;
	get_currentuserinfo();
	
	if ( !empty($_POST) ) {
		if($_POST['tarski_update_notification'] == __('Turn update notification off?', 'tarski')) {
			update_tarski_option('update_notification', 'false');
		}	elseif($_POST['tarski_update_notification'] == __('Turn update notification on?', 'tarski')) {
			update_tarski_option('update_notification', 'true');
		}
		
		if (isset($_POST['about_text'])) {
			$about = $_POST['about_text'];
			update_tarski_option('blurb', $about, '','');
		}
		if (isset($_POST['header_image'])) {
			$header = $_POST['header_image'];
			$header = @str_replace("-thumb", "", $header);
			update_tarski_option('header', $header, '','');
		}
		
		$nav_pages = implode(',', $_POST['nav_pages']);
		
		update_tarski_options(array(
			'footer_recent' => $_POST['footer']['recent'],
			'sidebar_pages' => $_POST['sidebar']['pages'],
			'sidebar_links' => $_POST['sidebar']['links'],
			'sidebar_comments' => $_POST['sidebar']['comments'],
			'sidebar_custom' => $_POST['sidebar']['custom'],
			'sidebar_onlyhome' => $_POST['sidebar']['onlyhome'],
			'display_title' => $_POST['display_title'],
			'display_tagline' => $_POST['display_tagline'],
			'hide_categories' => $_POST['hide_categories'],
			'use_pages' => $_POST['use_pages'],
			'centered_theme' => $_POST['centered_theme'],
			'swap_sides' => $_POST['swap_sides'],
			'asidescategory' => $_POST['asides_category'],
			'style' => $_POST['alternate_style'],
			'ajax_tags' => $_POST['ajax_tags'],
			'nav_pages' => $nav_pages,
			'sidebar_type' => $_POST['sidebartype']
		));
	}
}

// if we can't find Tarski installed let's go ahead and install all the options that run Tarski. This should run only one more time for all our existing users, then they will just be getting the upgrade function if it exists.
if (!get_tarski_option('installed')) {
	add_tarski_option('installed', $installedVersion);
	add_tarski_option('header', 'greytree.jpg');
	add_tarski_option('blurb', __('This is the about text', 'tarski'));
}

// Here we handle upgrading our users with new options and such. If tarski_installed is in the DB but the version they are running is lower than our current version, trigger this event.
elseif (get_tarski_option('installed') < $installedVersion) {
	if(get_tarski_option('installed') < 1.1) {
		add_tarski_option('asidescategory', '0');
	}
	update_tarski_option('installed', $installedVersion);
}

// This adds the Tarski Options page
add_action('admin_menu', 'tarski_addmenu');

function tarski_addmenu() {
	add_submenu_page('themes.php', __('Tarski Options','tarski'), __('Tarski Options','tarski'), 'edit_themes', 'tarski-options', 'tarski_admin');
}

function tarski_admin() {
	require(TEMPLATEPATH . '/library/options-page.php');
}

function tarski_get_output($code) {
	ob_start();
	eval($code);
	$return = ob_get_contents();
	ob_end_clean();
	return $return;
}

// ~fin~ ?>