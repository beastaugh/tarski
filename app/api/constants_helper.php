<?php

/**
 * tarski_output_constant() - Checks if constant exists and outputs it if it does.
 * 
 * This function must always either be wrapped by another
 * constants output function, as in the other functions in
 * this file, or have constants.php @included and the variable
 * the $constant parameter is set to declared global.
 * @since 1.5
 * @param string $constant
 * @param boolean|string $pre
 * @param boolean|string $post
 * @return string
 */
function tarski_output_constant($constant, $pre = false, $post = false) {
	if($constant) {
		echo $pre . $constant . $post;
	}
}

/**
 * tarski_output_headinclude() - Outputs $headerInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $headerInclude
 * @return string $headerInclude
 */
function tarski_output_headinclude() {
	global $headerInclude;
	tarski_output_constant($headerInclude);
}

/**
 * tarski_output_navbarinclude() - Adds $navbarInclude variable from constants.php to navbar.
 * 
 * @since 1.5
 * @param array $input
 * @global string $navbarInclude
 * @return array $navbarInclude
 */
function tarski_output_navbarinclude($navbar) {
	global $navbarInclude;
	
	if ( !check_input($navbar, 'array') )
		$navbar = array();
	
	if ( $navbarInclude )
		$navbar['navbarinclude'] = $navbarInclude;
	
	return $navbar;
}

/**
 * tarski_output_frontpageinclude() - Outputs $frontPageInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $frontPageInclude
 * @global string $completedBlurb
 * @return string $frontPageInclude
 */
function tarski_output_frontpageinclude() {
	global $frontPageInclude;
	global $completedBlurb;
	if(is_home() && !$completedBlurb) {
		$completedBlurb = true;
		tarski_output_constant($frontPageInclude);
	}
}

/**
 * tarski_output_postendinclude() - Outputs $postEndInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $postEndInclude
 * @return string $postEndInclude
 */
function tarski_output_postendinclude() {
	global $postEndInclude;
	if(is_single()) {
		tarski_output_constant($postEndInclude);
	}
}

/**
 * tarski_output_pageendinclude() - Outputs $pageEndInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $pageEndInclude
 * @return string $pageEndInclude
 */
function tarski_output_pageendinclude() {
	global $pageEndInclude;
	if(is_page()) {
		tarski_output_constant($pageEndInclude);
	}
}

/**
 * tarski_output_commentsforminclude() - Outputs $commentsFormInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $commentsFormInclude
 * @return string $commentsFormInclude
 */
function tarski_output_commentsforminclude() {
	global $commentsFormInclude;
	tarski_output_constant($commentsFormInclude);
}

/**
 * tarski_output_sidebartopinclude() - Outputs $sidebarTopInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $sidebarTopInclude
 * @return string $sidebarTopInclude
 */
function tarski_output_sidebartopinclude() {
	global $sidebarTopInclude;
	if(get_tarski_option('sidebar_onlyhome')) { // Sidebar only on index pages
		if(!(is_page() || is_single())) {
			tarski_output_constant($sidebarTopInclude);
		}
	} else { // Sidebar everywhere
		if(!(is_page() || is_single())) {
			tarski_output_constant($sidebarTopInclude);
		}
	}
}

/**
 * tarski_output_sidebarbottominclude() - Outputs $sidebarBottomInclude variable from constants.php.
 * 
 * @since 2.0
 * @global string $sidebarBottomInclude
 * @return string $sidebarBottomInclude
 */
function tarski_output_sidebarbottominclude() {
	global $sidebarBottomInclude;
	if(get_tarski_option('sidebar_onlyhome')) { // Sidebar only on index pages
		if(!(is_page() || is_single())) {
			tarski_output_constant($sidebarBottomInclude);
		}
	} else { // Sidebar everywhere
		if(!(is_page() || is_single())) {
			tarski_output_constant($sidebarBottomInclude);
		}
	}
}

/**
 * tarski_output_nosidebarinclude() - Outputs $noSidebarInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $noSidebarInclude
 * @return string $noSidebarInclude
 */
function tarski_output_nosidebarinclude() {
	global $noSidebarInclude;
	if((get_tarski_option('sidebar_pp_type') == 'none') && (is_single() || is_page())) {
		if(!is_page_template('archives.php')) {
			tarski_output_constant($noSidebarInclude);
		}
	}
}

/**
 * tarski_output_archivesinclude() - Outputs $archivesPageInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $archivesPageInclude
 * @return string $archivesPageInclude
 */
function tarski_output_archivesinclude() {
	global $archivesPageInclude;
	if(is_page_template('archives.php')) {
		tarski_output_constant($archivesPageInclude);
	}
}

/**
 * tarski_output_searchtopinclude() - Outputs $searchTopInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $searchTopInclude
 * @return string $searchTopInclude
 */
function tarski_output_searchtopinclude() {
	global $searchTopInclude;
	tarski_output_constant($searchTopInclude);
}

/**
 * tarski_output_searchbottominclude() - Outputs $searchBottomInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $searchBottomInclude
 * @return string $searchBottomInclude
 */
function tarski_output_searchbottominclude() {
	global $searchBottomInclude;
	tarski_output_constant($searchBottomInclude);
}

/**
 * tarski_output_footerinclude() - Outputs $footerInclude variable from constants.php.
 * 
 * @since 1.5
 * @global string $footerInclude
 * @return string $footerInclude
 */
function tarski_output_footerinclude() {
	global $footerInclude;
	tarski_output_constant($footerInclude, '<div id="footer-include">', '</div>');
}


/**
 * tarski_output_errorinclude() - Outputs $errorPageInclude variable from constants.php.
 * 
 * @since 1.5
 * @param string $input
 * @global string $errorPageInclude
 * @return string $output equal to $errorPageInclude or $input
 */
function tarski_output_errorinclude($input) {
	global $errorPageInclude;
	if($errorPageInclude) {
		$output = $errorPageInclude;
	} else {
		$output = $input;
	}
	return $output;
}

?>