<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Return the current theme version.
 *
 * @since 2.0
 * @return string
 */
function theme_version() {
    $themedata = get_theme_data(get_template_directory() . '/style.css');
    $version = trim($themedata['Version']);
    
    return strlen($version) > 0 ? $version : '';
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
 * If debug mode is enabled, use an uncompressed version of the file.
 *
 * @since 2.7
 *
 * @see TARSKI_DEBUG
 *
 * @param string $path
 * @return string
 */
function tarski_asset_path($path) {
    $matches = array();
    preg_match("/\\.[A-Za-z\d]+\$/", $path, &$matches);
    $ext     = count($matches) > 0 ? $matches[0] : '';
    $suffix  = defined('TARSKI_DEBUG') && TARSKI_DEBUG === true ? '.dev' : '';
    $root    = get_template_directory_uri();
    $path    = preg_replace("/${ext}\$/", '', $path);
    
    return $root . '/' . $path . $suffix . $ext;
}

/**
 * Return a list of header images, both from the Tarski directory and the child
 * theme (if one is being used).
 *
 * @uses get_tarski_option
 * @uses get_current_theme
 * @uses get_template_directory_uri
 * @uses get_stylesheet_directory_uri
 *
 * @return array
 */
function _tarski_list_header_images() {
    $headers = array();
    $dirs    = array('Tarski' => get_template_directory());
    $current = get_tarski_option('header');
    $theme   = get_current_theme();
    
    if (get_template_directory() != get_stylesheet_directory())
        $dirs[$theme] = get_stylesheet_directory();
    
    foreach ($dirs as $theme => $dir) {
        $dirpath = $dir . '/headers';
        
        if (is_dir($dirpath))
            $header_dir = dir($dirpath);
        else
            continue;
        
        while ($file = $header_dir->read()) {
            if (preg_match('/^[^.].+\.(jpg|png|gif)/', $file) &&
                !preg_match('/-thumb\.(jpg|png|gif)$/', $file)) {
                $name  = $theme . '/' . $file;
                $id    = 'header_' . preg_replace('/[^a-z_]/', '_', strtolower($name));
                $path  = $dir == get_template_directory() ? '%1$s' : '%2$s';
                $thumb = preg_replace('/(\.(?:png|gif|jpg))/', '-thumb\\1', $file);
                $uri   = ($dir == get_template_directory()
                       ? get_template_directory_uri()
                       : get_stylesheet_directory_uri());
                $is_current = is_string($current) && $current == $file ||
                              $current[0] == $theme && $current[1] == $file;
                $headers[] = array(
                    'name'    => $name,
                    'id'      => $id,
                    'lid'     => 'for_' . $id,
                    'path'    => "$uri/headers/$file",
                    'current' => $is_current,
                    'thumb'   => "$uri/headers/$thumb",
                    
                    // New fields for core header selector
                    'description'   => $name,
                    'url'           => "$path/headers/$file",
                    'thumbnail_url' => "$path/headers/$thumb");
            }
        }
    }
    
    return $headers;
}

?>