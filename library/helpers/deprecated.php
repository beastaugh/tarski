<?php

/**
 * The scrapyard: deprecated functions that haven't yet been removed
 */


/**
 * get_tarski_header_status() - Returns current header status.
 * 
 * Output is currently used to set an HTML class, which allows
 * the way the theme displays to be tweaked through CSS.
 * @deprecated since 2.0
 * @since 1.7
 * @see tarski_header_status()
 * @return string
 */
function get_tarski_header_status() {
	tarski_header_status(true);
}

/**
 * get_tarski_navbar() - Returns the Tarski navbar.
 * 
 * @deprecated since 2.0
 * @see tarski_navbar()
 * @return string
 */
function get_tarski_navbar() {
	tarski_navbar(true);
}

/**
 * tarski_navbar_feedlink() - Outputs feed link for the Tarski navbar.
 * 
 * @deprecated since 2.0
 * @see tarski_feedlink()
 * @return string
 */
function tarski_navbar_feedlink($return = false) {
	tarski_feedlink();
}

/**
 * get_tarski_bodyclass() - Outputs the classes that should be applied to the document body.
 * 
 * @see tarski_bodyclass()
 * @return string
 */
function get_tarski_bodyclass() {
	tarski_bodyclass(true);
}

/**
 * get_tarski_bodyid() - Returns the id that should be applied to the document body.
 * 
 * @deprecated since 2.0
 * @see tarski_bodyid()
 * @return string
 */
function get_tarski_bodyid() {
	tarski_bodyid(true);
}

/**
 * tarski_get_output() - Returns the output of functions that only echo.
 * 
 * This output-buffering function is a horrible hack and fortunately
 * can now (2.0) be deprecated.
 * @deprecated since 2.0
 * @global object $comment
 * @global object $post
 */
function tarski_get_output($code) {
	global $comment, $post;
	ob_start();
	@eval($code);
	$return = ob_get_contents();
	ob_end_clean();
	return $return;
}

/**
 * link_pages_without_spaces() - Returns page links without the spaces WordPress seems to love.
 * 
 * @deprecated since 2.0
 * @see tarski_link_pages()
 * @return string
 */
function link_pages_without_spaces($return = false) {
	if(!in_category(get_tarski_option('asidescategory'))) {
		tarski_get_output(link_pages('<p class="pagelinks"><strong>Pages</strong>', '</p>', 'number', '', '', '%', ''));
	
		$text = str_replace(' <a href', '<a href', $text);
		$text = str_replace('> ', '>', $text);
		apply_filters('link_pages_without_spaces', $text);
		if($return) {
			return $text;
		} else {
			echo $text;
		}
	}
}

/**
 * tarski_next_prev_pages() - Links to next and previous index pages.
 * 
 * @deprecated since 2.0
 * @see
 * @return string
 */
function tarski_next_prev_pages() {
	tarski_posts_nav_link();
}


/**
 * get_tarski_footer_blurb() - Outputs custom sidebar text.
 *
 * @deprecated since 2.0
 * @see get_tarski_footer_blurb()
 * @return string
 */
function get_tarski_footer_blurb() {
	tarski_footer_blurb(true);
}

/**
 * tarski_feed_and_credit() - Outputs feed link and Tarski credits
 * 
 * @deprecated since 2.0
 * @since 1.5
 * @see tarski_feedlink()
 * @see tarski_credits()
 */
function tarski_feed_and_credit() {
	tarski_feedlink();
	tarski_credits();
}

?>