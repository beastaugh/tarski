<?php

/**
 * Tarski header hook
 *
 * Template function appearing in header.php, allows actions
 * to be executed at that point in the template. By default
 * used to add header images and the title and tagline.
 * @example add_action('th_header', 'my_function');
 **/
function th_header() {
	do_action('th_header');
}

/**
 * Tarski navbar hook
 *
 * Template function appearing in header.php, allows actions
 * to be executed at that point in the template. By default
 * used to add the navbar and site feed link.
 * @example add_action('th_navbar', 'my_function');
 **/
function th_navbar() {
	do_action('th_navbar');
}

/**
 * Tarski post end hook
 *
 * Template function appearing at the end of each post,
 * to be executed at that point in the template. By default
 * used to add tags, page navigation and the $postEndInclude
 * and $pageEndInclude constants.
 * @example add_action('th_postend', 'my_function');
 **/
function th_postend() {
	do_action('th_postend');
}

/**
 * Tarski sidebar hook
 *
 * Template function appearing in sidebar.php, allows actions
 * to be executed at that point in the template. By default
 * used to output the $sidebarTopInclude and $noSidebarInclude
 * constants.
 * @example add_action('th_sidebar', 'my_function');
 **/
function th_sidebar() {
	do_action('th_sidebar');
}

/**
 * Tarski footer sidebar hook
 *
 * Template function appearing in footer.php, allows actions
 * to be executed at that point in the template. By default
 * used to add a search form.
 * @example add_action('th_fsidebar', 'my_function');
 **/
function th_fsidebar() {
	do_action('th_fsidebar');
}

/**
 * Tarski footer hook
 *
 * Template function appearing in footer.php, allows actions
 * to be executed at that point in the template. By default
 * used to add the theme credit and site feed link.
 * @example add_action('th_footer', 'my_function');
 **/
function th_footer() {
	do_action('th_footer');
}

?>