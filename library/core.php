<?php

/**
 * check_input() - Checks input is of correct type
 * 
 * Always returns true when WP_DEBUG is true, to allow for easier debugging
 * in the development environment while handling erroneous input more
 * robustly in the production environment.
 * @see http://uk3.php.net/manual/en/function.gettype.php
 * @since 2.1
 * @param mixed $input
 * @param string $type
 * @param string $name
 * @return boolean
 *
 */
function check_input($input, $type, $name = '') {
	if ( WP_DEBUG === true )
		return true;

	if ( $type == 'object' && strlen($name) > 0 )
		return is_a($input, $name);
	else
		return call_user_func("is_$type", $input);
}

/**
 * theme_version() - Returns the current theme version.
 * 
 * @since 2.0
 * @return string
 */
function theme_version() {
	$themedata = get_theme_data(TEMPLATEPATH . '/style.css');
	$version = trim($themedata['Version']);
	
	if (strlen($version) > 0)
		return $version;
}

/**
 * detectWPMU() - Detects whether WordPress Multi-User is in use.
 * 
 * @since 1.4
 * @return boolean
 */
function detectWPMU() {
	return function_exists('is_site_admin');
}

/**
 * is_valid_tarski_style() - Checks whether a given file name is a valid Tarski stylesheet name.
 * 
 * It must be a valid CSS identifier, followed by the .css file extension,
 * and it cannot have a name that is already taken by Tarski's CSS namepsace.
 * @since 2.0
 * @param string $file
 * @return boolean
 */
function is_valid_tarski_style($file) {
	return (bool) (
		!preg_match('/^\.+$/', $file)
		&& preg_match('/^[A-Za-z][A-Za-z0-9\-]*.css$/', $file)
		&& !preg_match('/^(janus|centre|rtl|js).css$/', $file)
	);
}

?>