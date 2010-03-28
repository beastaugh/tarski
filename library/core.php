<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Locate a template somewhere in the Tarski template search path. The search
 * path itself by default includes the current theme's /templates directory,
 * and Tarski's /app/templates directory. The function returns the path of the
 * first template of the given name to be located.
 *
 * @since 2.7
 *
 * @uses apply_filters
 * @see tarski_template
 *
 * @param string $template
 * @return string
 *
 * @hook filter tarski_template_paths
 * Allows for the addition of new template stores to supplement those provided
 * by Tarski itself and any child theme. Simply add a new file system path to
 * the beginning of the array passed to filters added to the
 * tarski_template_paths hook using e.g. array_unshift.
 */
function tarski_locate_template($template) {
    $default_search_paths = array(
        STYLESHEETPATH . "/templates",
        TARSKIDISPLAY);
    
    $search_paths = apply_filters('tarski_template_paths',
        $default_search_paths);
    
    while ($path = next($search_paths) . "/${template}") {
        if (file_exists($path)) break;
    }
    
    return $path;
}

/**
 * Include a template located somewhere in the Tarski template search path.
 *
 * @since 2.7
 *
 * @uses tarski_locate_template
 *
 * @param string $filename
 * @return void
 */
function tarski_template($filename) {
    $path = tarski_locate_template($filename);
    if ($path) include($path);
}

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
 * If debug mode is enabled, use uncompressed (development mode) JavaScript.
 *
 * @since 2.7
 *
 * @see TARSKI_DEBUG
 * @uses _tarski_compressible_asset_path
 *
 * @param string $path
 * @return string
 */
function tarski_js($path) {
    return _tarski_compressible_asset_path('js', $path);
}

/**
 * If debug mode is enabled, use uncompressed (development mode) CSS.
 *
 * @since 2.7
 *
 * @see TARSKI_DEBUG
 * @uses _tarski_compressible_asset_path
 *
 * @param string $path
 * @return string
 */
function tarski_css($path) {
    return _tarski_compressible_asset_path('css', $path);
}

/**
 * If debug mode is enabled, use an uncompressed version of the file.
 *
 * @since 2.7
 *
 * @see TARSKI_DEBUG
 * @see tarski_js
 * @see tarski_css
 *
 * @param string $path
 * @return string
 */
function _tarski_compressible_asset_path($type, $path) {
    $dev  = defined('TARSKI_DEBUG') && TARSKI_DEBUG === true ? '.dev' : '';
    $path = preg_replace("/\.${type}$/", '', $path);
    
    return $path . $dev . ".${type}";
}

?>