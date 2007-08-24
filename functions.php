<?php // functions.php - Tarski functions library

global $tarski_options;
flush_tarski_options();

// Version detection
function theme_version() {
	$themeData = get_theme_data(TEMPLATEPATH . '/style.css');
	$installedVersion = trim($themeData['Version']);
	if($installedVersion == false) {
		return "unknown";
	} else {
		return $installedVersion;
	}
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
		update_tarski_option('sidebar_type','tarski');
	}
}

// Constants file include
@include(TEMPLATEPATH . '/constants.php');

// Functions and hooks - important!
require(TEMPLATEPATH . '/library/template-functions.php');
require(TEMPLATEPATH . '/library/content-functions.php');
require(TEMPLATEPATH . '/library/tarski-hooks.php');
require(TEMPLATEPATH . '/library/constants-hooks.php');
require(TEMPLATEPATH . '/library/update-notifier.php');

// Localisation
load_theme_textdomain('tarski');

// Options page and dashboard injections
add_action('admin_head', 'tarski_inject_scripts');
if(!detectWPMU()) {
	add_action('activity_box_end','update_notifier_dashboard');
}

// Widgets
if(function_exists('register_sidebar')) {
	register_sidebar(array(
		'name' => __('Main Sidebar','tarski'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => __('Footer Widgets','tarski'),
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

// Options page JS and CSS injection
function tarski_inject_scripts() {
	if(substr($_SERVER['REQUEST_URI'], -39, 39) == 'wp-admin/themes.php?page=tarski-options') { // Ugly
		echo "\n\n";
		echo '<link rel="stylesheet" href="' . get_bloginfo('template_directory') . '/library/options.css" type="text/css" media="screen" />' . "\n";
		echo '<script src="' . get_bloginfo('wpurl') . '/wp-includes/js/jquery/jquery.js' . '" type="text/javascript"></script>' . "\n";
		echo '<script src="' . get_bloginfo('template_directory') . '/library/crir.js' .'" type="text/javascript"></script>' . "\n";
		echo '<script src="' . get_bloginfo('template_directory') . '/library/options.js' .'" type="text/javascript"></script>' . "\n";
		echo "\n";
	}
}

// Update function
// I r serious cat. This r serious function.
function tarskiupdate() {
	global $wpdb, $user_ID;
	get_currentuserinfo();
	
	if ( !empty($_POST) ) {
		if($_POST['tarski_update_notification'] == __('Turn update notification off?','tarski')) {
			update_tarski_option('update_notification', 'false');
		}	elseif($_POST['tarski_update_notification'] == __('Turn update notification on?','tarski')) {
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
		
		$nav_pages = implode(",", $_POST['nav_pages']);
		
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

// Keeps the navbar order current with the page order
function tarski_resave_navbar() {
	global $wpdb;
	$pages = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page' ORDER BY post_parent, menu_order");
	$selected = explode(',', get_tarski_option("nav_pages"));

	if($pages) {
		$nav_pages = array();
		foreach ($pages as $key => $page) {
			foreach($selected as $key2 => $sel_page) {
				if ($page->ID == $sel_page) {
					$nav_pages[$key] = $page->ID;
				}
			}
		}
		$condensed = implode(",", $nav_pages);
	}
	
	update_tarski_option("nav_pages", $condensed);
}

add_action("save_post","tarski_resave_navbar");

// if we can't find Tarski installed let's go ahead and install all the options that run Tarski. This should run only one more time for all our existing users, then they will just be getting the upgrade function if it exists.
if (!get_tarski_option('installed')) {
	add_tarski_option('installed', theme_version());
	add_tarski_option('header', 'greytree.jpg');
	add_tarski_option('blurb', __('This is the about text','tarski'));
	add_tarski_option('update_notification','true');
}

// Here we handle upgrading our users with new options and such. If tarski_installed is in the DB but the version they are running is lower than our current version, trigger this event.
elseif (get_tarski_option('installed') < theme_version()) {
	if(get_tarski_option('installed') < 1.1) {
		add_tarski_option('asidescategory', '0');
	}
	if(!get_tarski_option('update_notification','false') &&!get_tarski_option('update_notification','true')) {
		add_tarski_option('update_notification','true');
	}
	update_tarski_option('installed', theme_version());
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
	global $comment, $post;
	
	ob_start();
	@eval($code);
	$return = ob_get_contents();
	ob_end_clean();
	return $return;
}

// ~fin~ ?>