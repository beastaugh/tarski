<?php // Ties Tarski's hooks into the legacy constants file

// General constants check
function tarski_output_constant($constant,$pre=false,$post=false) {
	if($constant) {
		echo $pre. $constant. $post;
	}
}

// Output $headerInclude
function tarski_output_headinclude() {
	global $headerInclude;
	tarski_output_constant($headerInclude);
}

// Output $navbarInclude
function tarski_output_navbarinclude($input) {
	global $navbarInclude;
	if($navbarInclude) {
		$input .= $navbarInclude."\n";
	}
	return $input;
}

// Output $frontPageInclude
function tarski_output_frontpageinclude() {
	global $frontPageInclude;
	global $completedBlurb;
	
	if(is_home() && !$completedBlurb) {
		$completedBlurb = true;
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
function tarski_output_errorinclude($input) {
	global $errorPageInclude;
	if($errorPageInclude) {
		$output = $errorPageInclude;
	} else {
		$output = $input;
	}
	return $output;
}

// ~fin~ ?>