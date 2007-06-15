<?php // constants-hooks.php - Tying Tarski's hooks into the legacy constants file

// General constants check
function tarski_output_constant($constant,$pre=false,$post=false) {
	if($constant) {
		echo $pre . $constant . $post;
	}
}

// Output $headerInclude
function tarski_output_headinclude() {
	global $headerInclude;
	tarski_output_constant($headerInclude);
}

// Output $frontPageInclude
function tarski_output_frontpageinclude() {
	global $frontPageInclude;
	global $completedBlurb;
	
	if(is_home() && !$completedBlurb) {
		$completedBlurb = 1;
		tarski_output_constant($frontPageInclude);
	}
}

// Output $postEndInclude
function tarski_output_postendinclude() {
	global $postEndInclude;
	if(is_single()) {
		tarski_output_constant($postEndInclude);
	}
}

// Output $pageEndInclude
function tarski_output_pageendinclude() {
	global $pageEndInclude;
	if(is_page()) {
		tarski_output_constant($pageEndInclude);
	}
}

// Output $pageEndInclude
function tarski_output_commentsforminclude() {
	global $commentsFormInclude;
	tarski_output_constant($commentsFormInclude);
}

// Output $sidebarTopInclude
function tarski_output_sidebartopinclude() {
	global $sidebarTopInclude;
	global $post;
	if(get_tarski_option('sidebar_onlyhome')) { // Sidebar only on index pages
		if(!(is_page() || is_single())) {
			tarski_output_constant($sidebarTopInclude);
		}
	} else { // Sidebar everywhere
		if(!(is_page() || is_single())) {
			tarski_output_constant($sidebarTopInclude);
		} elseif(get_post_meta($post->ID,'_wp_page_template',true) != 'archives.php') {
			tarski_output_constant($sidebarTopInclude);
		}
	}
}

// Output $noSideBarInclude
function tarski_output_nosidebarinclude() {
	global $noSidebarInclude;
	global $post;
	if(get_tarski_option('sidebar_onlyhome') && (is_single() || is_page())) {
		if(get_post_meta($post->ID,'_wp_page_template',true) != 'archives.php') {
			tarski_output_constant($noSidebarInclude);
		}
	}
}

// Output $archivesPageInclude
function tarski_output_archivesinclude() {
	global $archivesPageInclude;
	global $post;
	if(get_post_meta($post->ID,'_wp_page_template',true) == 'archives.php') {
		tarski_output_constant($archivesPageInclude);
	}
}

// Output $searchTopInclude
function tarski_output_searchtopinclude() {
	global $searchTopInclude;
	tarski_output_constant($searchTopInclude);
}

// Output $searchBottomInclude
function tarski_output_searchbottominclude() {
	global $searchBottomInclude;
	tarski_output_constant($searchBottomInclude);
}

// Output $footerInclude
function tarski_output_footerinclude() {
	global $footerInclude;
	tarski_output_constant($footerInclude,'<div id="footer-include">','</div>');
}


// Output $errorPageInclude
function tarski_output_errorinclude() {
	global $errorPageInclude;
	if($errorPageInclude) {
		echo $errorPageInclude;
	} else {
		echo '<p>' . __('The page you are looking for does not exist; it may have been moved, or removed altogether. You might want to try the search function or return to the ','tarski') . '<a href="' . get_settings('home') . '">' . __('front page','tarski') . '</a>' . __('.','tarski') . "</p>\n";
	}
}

add_action('wp_head','tarski_output_headinclude');
add_action('th_postend','tarski_output_frontpageinclude');
add_action('th_postend','tarski_output_postendinclude');
add_action('th_postend','tarski_output_pageendinclude');
add_action('th_commentform','tarski_output_commentsforminclude',11);
add_action('th_sidebar','tarski_output_sidebartopinclude');
add_action('th_sidebar','tarski_output_nosidebarinclude');
add_action('th_sidebar','tarski_output_archivesinclude');
add_action('th_fsidebar','tarski_output_searchtopinclude',9);
add_action('th_fsidebar','tarski_output_searchbottominclude',11);
add_action('th_footer','tarski_output_footerinclude');
add_action('th_404','tarski_output_errorinclude');

// ~fin~ ?>