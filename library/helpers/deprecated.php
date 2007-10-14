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