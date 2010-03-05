<?php

/**
 * Return the current theme version.
 * 
 * @since 2.0
 * @return string
 */
function theme_version() {
	$themedata = get_theme_data(TEMPLATEPATH . '/style.css');
	$version = trim($themedata['Version']);
	
	return strlen($version) > 0 ? $version : '';
}

/**
 * Detect whether WordPress Multi-User is in use.
 * 
 * @since 1.4
 * @return boolean
 */
function detectWPMU() {
	return function_exists('is_site_admin');
}

/**
 * Check whether a given file name is a valid Tarski stylesheet name.
 * 
 * It must be a valid CSS identifier, followed by the .css file extension,
 * and it cannot have a name that is already taken by Tarski's CSS namespace.
 *
 * @since 2.0
 * @param string $name
 * @return boolean
 */
function is_valid_tarski_style($name) {
	$file = array_pop(preg_split('/\//', $name));
	return !preg_match('/^\.+$/', $file) &&
		preg_match('/^[A-Za-z][A-Za-z0-9\-]*.css$/', $file) &&
		!preg_match('/^(janus|centre|rtl|js).css$/', $file);
}

/**
 * Appends "-min" to the filename of the JavaScript file path passed in.
 *
 * @since 2.7
 *
 * @see TARSKI_DEBUG
 *
 * @param string $path
 * @return string
 */
function tarski_js($path) {
    $min  = defined('TARSKI_DEBUG') && TARSKI_DEBUG === true ? '' : '-min';
    $path = preg_replace('/\.js$/', '', $path);
    
    return $path . $min . '.js';
}

?>