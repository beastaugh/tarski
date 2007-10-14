<?php

/**
 * th_header() - Tarski header hook
 *
 * Template function appearing in header.php, allows actions
 * to be executed at that point in the template. By default
 * used to add header images and the title and tagline.
 * @example add_action('th_header', 'my_function');
 * @since 1.5
 **/
function th_header() {
	do_action('th_header');
}

/**
 * th_navbar() - Tarski navbar hook
 *
 * Template function appearing in header.php, allows actions
 * to be executed at that point in the template. By default
 * used to add the navbar and site feed link.
 * @example add_action('th_navbar', 'my_function');
 * @since 1.5
 **/
function th_navbar() {
	do_action('th_navbar');
}

/**
 * th_postend() - Tarski post end hook
 *
 * Template function appearing at the end of each post,
 * to be executed at that point in the template. By default
 * used to add tags, page navigation and the $postEndInclude
 * and $pageEndInclude constants.
 * @example add_action('th_postend', 'my_function');
 * @since 1.5
 **/
function th_postend() {
	do_action('th_postend');
}

/**
 * th_sidebar() - Tarski sidebar hook
 *
 * Template function appearing in sidebar.php, allows actions
 * to be executed at that point in the template. By default
 * used to output the $sidebarTopInclude and $noSidebarInclude
 * constants.
 * @example add_action('th_sidebar', 'my_function');
 * @since 1.5
 **/
function th_sidebar() {
	do_action('th_sidebar');
}

/**
 * th_fsidebar() - Tarski footer sidebar hook
 *
 * Template function appearing in footer.php, allows actions
 * to be executed at that point in the template. By default
 * used to add a search form.
 * @example add_action('th_fsidebar', 'my_function');
 * @since 1.5
 **/
function th_fsidebar() {
	do_action('th_fsidebar');
}

/**
 * th_footer() - Tarski footer hook
 *
 * Template function appearing in footer.php, allows actions
 * to be executed at that point in the template. By default
 * used to add the theme credit and site feed link.
 * @example add_action('th_footer', 'my_function');
 * @since 1.5
 **/
function th_footer() {
	do_action('th_footer');
}

?>