<?php

/**
 * th_header() - Tarski header hook
 *
 * Template function appearing in header.php, allows actions
 * to be executed at that point in the template. By default
 * used to add header images and the title and tagline.
 * @example add_action('th_header', 'my_function');
 * @since 1.5
 * @hook action th_header
 * One can use this hook to print additional header content. Tarski uses it
 * internally to add the header image, site title, tagline and navbar.
 */
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
 * @hook action th_navbar
 * Executed by a wrapper function added to the th_header hook,
 * this hook is used to add content to the navbar.
 */
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
 * @hook action th_postend
 * This hook is used to add content to the end of posts. Tarski uses it
 * internally to add tags to posts and navigation for multi-page posts.
 */
function th_postend() {
	do_action('th_postend');
}

/**
 * th_posts_nav - Tarski posts navigation hook
 * 
 * Template function appearing at the end of the loop,
 * to be executed at that point in the template. By default
 * used to add next and previous posts navigation on index pages.
 * @since 2.1
 * @hook action th_posts_nav
 * Tarski uses this hook to add the next and previous posts navigation on index
 * pages. It could be used to replace this navigation with some other kind.
 */
function th_posts_nav() {
	do_action('th_posts_nav');
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
 * @hook action th_sidebar
 * The sidebar hook is used by Tarski to add different sidebars, depending on
 * the options chosen by the user. It can be used to override the default
 * sidebar code.
 */
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
 * @hook action th_fsidebar
 * Like th_sidebar, but for the footer, this hook is used to add widgets
 * to the secondary footer area.
 */
function th_fsidebar() {
	do_action('th_fsidebar');
}

/**
 * th_fmain() - Tarski footer main section hook
 *
 * Template function appearing in footer.php, allows actions
 * to be executed at that point in the template. By default
 * used to add the footer blurb and recent entries
 * @example add_action('th_fmain', 'my_function');
 * @since 2.0.5
 * @hook action th_fmain
 * Similar to th_fsidebar and th_sidebar, this hook is used to add widgets to
 * the primary footer area.
 */
function th_fmain() {
	do_action('th_fmain');
}

/**
 * th_footer() - Tarski footer hook
 *
 * Template function appearing in footer.php, allows actions
 * to be executed at that point in the template. By default
 * used to add the theme credit and site feed link.
 * @example add_action('th_footer', 'my_function');
 * @since 1.5
 * @hook action th_footer
 * Used by default to add the theme credit and site feed link.
 */
function th_footer() {
	do_action('th_footer');
}

?>