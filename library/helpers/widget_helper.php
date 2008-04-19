<?php

/**
 * tarski_sidebar() - Outputs Tarski's sidebar.
 * 
 * @since 2.0
 * @global object $post
 * @return mixed
 */
function tarski_sidebar() {
	global $post;
	$user_sidebar = TEMPLATEPATH . '/user-sidebar.php';
	
	if ( file_exists($user_sidebar) ) {
		include($user_sidebar);
	} elseif ( is_page_template('archives.php') ) {
		return;
	} else {
		if ( (is_single() || is_page()) && (get_tarski_option('sidebar_pp_type') != 'main') )
			dynamic_sidebar('sidebar-post-and-page');
		else
			dynamic_sidebar('sidebar-main');
	}
}

/**
 * tarski_footer_main() - Outputs footer main widgets field.
 * 
 * @since 2.1
 * @return mixed
 */
function tarski_footer_main() {
	dynamic_sidebar('footer-main');
}

/**
 * tarski_footer_sidebar() - Outputs the footer sidebar widgets field.
 * 
 * @since 2.0
 * @return mixed
 */
function tarski_footer_sidebar() {
	dynamic_sidebar('footer-sidebar');
}

/**
 * tarski_widget_text_wrapper() - Wraps text widgets in content div with edit link.
 *
 * @since 2.1
 * @param string $text
 * @return string
 */
function tarski_widget_text_wrapper($text) {
	if ( strlen(trim($text)) )
		$text = "<div class=\"content\">\n$text</div>\n";
	
	return $text;
}

/**
 * tarski_widget_links() - Tarski links widget.
 *
 * Doesn't display links from the category being used in the navbar,
 * if one is set.
 * @since 2.1
 * @see wp_widget_links()
 * @param array $args
 * @return string
 */
function tarski_widget_links($args) {
	extract($args, EXTR_SKIP);
	wp_list_bookmarks(array_merge(tarski_sidebar_links(), array('category_before' => $before_widget, 'category_after' => $after_widget)));
}

/**
 * tarski_widget_search() - Replaces the default search widget.
 *
 * Hopefully temporary, a patch has been proposed for WP 2.6
 * which does the same thing.
 * @since 2.1
 * @link http://trac.wordpress.org/ticket/5567
 */
function tarski_widget_search($args) {
	extract($args, EXTR_SKIP);
	$searchform_template = get_template_directory() . '/searchform.php';

	if ( !file_exists($searchform_template) )
		$searchform_template = get_theme_root() . '/default/searchform.php';

	echo $before_widget;
	include_once($searchform_template);
	echo $after_widget;
}

?>