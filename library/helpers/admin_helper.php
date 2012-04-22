<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Saves a new set of Tarski options.
 *
 * The primary request handler for the Tarski options system. Saves any updated
 * options and redirects to the options page.
 *
 * @since 2.0
 *
 * @see tarskiupdate() which it replaces
 * @see delete_tarski_options()
 * @see restore_tarski_options()
 */
function save_tarski_options() {
    check_admin_referer('admin_post_tarski_options', '_wpnonce_tarski_options');
    
    if (!current_user_can('manage_options'))
        wp_die(__('You are not authorised to perform this operation.', 'tarski'));
    
    $options = flush_tarski_options();
    $options->tarski_options_update();
    update_option('tarski_options', $options);
    
    wp_redirect(admin_url('themes.php?page=tarski-options&updated=true'));
}

/**
 * Sets the 'deleted' property on Tarski's options.
 *
 * A secondary request handler for the Tarski options system. Sets the
 * 'deleted' property in the options object to the current time and redirects
 * to the options page.
 *
 * @since 2.4
 *
 * @see save_tarski_options()
 * @see restore_tarski_options()
 * @see maybe_wipe_tarski_options()
 */
function delete_tarski_options() {
    check_admin_referer('admin_post_delete_tarski_options', '_wpnonce_delete_tarski_options');
    
    if (!current_user_can('manage_options'))
        wp_die(__('You are not authorised to perform this operation.', 'tarski'));
    
    $options = flush_tarski_options();
    
    if (!is_numeric($options->deleted) || $options->deleted < 1) {
        $options->deleted = time();
        update_option('tarski_options', $options);
    }
    
    wp_redirect(admin_url('themes.php?page=tarski-options&deleted=true'));
}

/**
 * Unsets the 'deleted' property on Tarski's options.
 *
 * A secondary request handler for the Tarski options system. Unsets the
 * 'deleted' property in the options object and redirects to the options page.
 *
 * @since 2.4
 *
 * @see save_tarski_options()
 * @see delete_tarski_options()
 */
function restore_tarski_options() {
    check_admin_referer('admin_post_restore_tarski_options', '_wpnonce_restore_tarski_options');
    
    if (!current_user_can('manage_options'))
        wp_die(__('You are not authorised to perform this operation.', 'tarski'));
    
    $options = flush_tarski_options();
    
    if (is_numeric($options->deleted) && $options->deleted > 0) {
        unset($options->deleted);
        update_option('tarski_options', $options);
    }
    
    wp_redirect(admin_url('themes.php?page=tarski-options&restored=true'));    
}

/**
 * Wipes Tarski's options if the restoration window has elapsed.
 *
 * When the user resets Tarski's options, the 'deleted' property on the options
 * object is set to the current time. After three hours have elapsed (during
 * which time the user may restore their options), the tarski_options row in
 * the wp_options table will be deleted entirely by this function.
 *
 * @since 2.4
 *
 * @see delete_tarski_options()
 * @see restore_tarski_options()
 * @uses flush_tarski_options()
 */
function maybe_wipe_tarski_options() {
    $options = flush_tarski_options();
    $del = $options->deleted;
    
    if (is_numeric($del) && (time() - $del) > (3 * 3600)) {
        delete_option('tarski_options');
        flush_tarski_options();
    }
}

/**
 * Determines whether Tarski needs upgrading.
 *
 * 'Needs upgrading' is defined as having either no installed version,
 * or having an installed version with a lower version number than the
 * version number extracted from the main stylesheet.
 *
 * @since 2.1
 *
 * @uses get_option
 * @uses get_tarski_option
 * @uses wp_get_theme
 *
 * @return boolean
 */
function tarski_upgrade_needed() {
    if (!get_option('tarski_options')) return false;
    $installed = get_tarski_option('installed');
    return empty($installed) || version_compare($installed, wp_get_theme()->Version) === -1;
}

/**
 * Upgrades Tarski if needed and flushes options.
 *
 * @since 2.1
 *
 * @see tarski_upgrade_needed
 * @see tarski_upgrade
 * @uses tarski_upgrade_needed
 * @uses tarski_upgrade
 * @uses flush_tarski_options
 */
function tarski_upgrade_and_flush_options() {
    if (tarski_upgrade_needed()) {
        tarski_upgrade();
        flush_tarski_options();
    }
}

/**
 * Upgrades Tarski options special cases.
 *
 * @since 2.3
 *
 * @see tarski_upgrade
 * @uses tarski_should_show_authors
 *
 * @param object $options
 * @param object $defaults
 */
function tarski_upgrade_special($options, $defaults) {
    if (empty($options->centred_theme) && isset($options->centered_theme))
        $options->centred_theme = true;
    
    if (empty($options->show_categories) && isset($options->hide_categories) && ($options->hide_categories == 1))
        $options->show_categories = false;
}

/**
 * Tarski preferences sometimes change between versions, and need to
 * be updated. This function does not determine whether an update is
 * needed, it merely perfoms it. It's also self-contained, so it
 * won't update the global $tarski_options object either.
 *
 * @since 2.1
 *
 * @uses tarski_upgrade_special
 */
function tarski_upgrade() {
    // Get options and set defaults
    $options = get_option('tarski_options');
    
    // Update the options version so we don't run this code more than once
    $options->installed = wp_get_theme()->Version;
    
    // Handle special cases first
    tarski_upgrade_special($options, null);
    
    // Save our upgraded options
    update_option('tarski_options', flush_tarski_options());
}

/**
 * Adds the Tarski Options page to the WordPress admin panel.
 *
 * @since 1.0
 */
function tarski_addmenu() {
    $page = add_theme_page(
        __('Tarski Options','tarski'),
        __('Tarski Options','tarski'),
        'manage_options',
        'tarski-options',
        'tarski_admin');
    
    add_action("admin_print_styles-$page", 'tarski_inject_styles');
}

/**
 * Displays the Options page.
 *
 * @since 1.0
 *
 * @uses current_user_can
 * @see WP_Http
 */
function tarski_admin() {
    if (current_user_can('manage_options'))
        get_template_part('app/templates/options_page');
}

/**
 * Styles the custom header image admin page for use with Tarski.
 *
 * @since 1.4
 */
function tarski_admin_header_style() { ?>
    <style type="text/css">
    #headimg {
        height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
        width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
    }
    #headimg h1, #headimg #desc {
        display: none;
    }
    </style>
<?php }

/**
 * Adds CSS to the Tarski Options page.
 *
 * @since 2.1
*/
function tarski_inject_styles() {
    wp_enqueue_style(
        'tarski_options',
        get_template_directory_uri() . '/library/css/options.css',
        array(), false, 'screen'
    );
}

/**
 * Return a list of alternate stylesheets, both from the Tarski directory and
 * the child theme (if one is being used).
 *
 * @uses get_tarski_option
 * @uses is_valid_tarski_style
 * @uses wp_get_theme
 *
 * @return array
 */
function _tarski_list_alternate_styles() {
    $styles        = array();
    $dirs          = array('Tarski' => get_template_directory());
    $current_style = get_tarski_option('style');
    $current_theme = wp_get_theme()->Name;
    
    if (get_template_directory() != get_stylesheet_directory())
        $dirs[$current_theme] = get_stylesheet_directory();
    
    foreach ($dirs as $theme => $dir) {
        $dirpath = $dir . '/styles';
        
        if (is_dir($dirpath))
            $style_dir = dir($dirpath);
        else
            continue;
        
        while ($file = $style_dir->read()) {
            if (is_valid_tarski_style($file)) {
                $is_current = (is_string($current_style)    &&
                               $current_theme == 'Tarski'   &&
                               $current_style == $file)
                            || ($current_style[0] == $theme &&
                                $current_style[1] == $file);
                $prefix = $theme . '/';
                $styles[] = array(
                    'name'    => $prefix . $file,
                    'public'  => ($theme == 'Tarski' ? '' : $prefix) . $file,
                    'current' => $is_current);
            }
        }
    }
    
    return $styles;
}

/**
 * Returns a list of checkboxes for miscellaneous options.
 *
 * Used for options that don't really fit anywhere else.
 *
 * @since 2.4
 *
 * @uses tarski_option_checkbox
 *
 * @return string
 */
function tarski_miscellaneous_options() {
    $output     = '';
    $checkboxes = array(
        'display_title'    => __('Display site title', 'tarski'),
        'display_tagline'  => __('Display site tagline', 'tarski'),
        'show_categories'  => __('Show post categories', 'tarski'),
        'tags_everywhere'  => __('Show tags everywhere', 'tarski'),
        'centred_theme'    => __('Centrally align the theme', 'tarski'),
        'swap_sides'       => __('Switch column order', 'tarski'),
        'swap_title_order' => __('Reverse document title order', 'tarski'),
        'featured_header'  => __('Display featured images in header', 'tarski'));
    
    foreach ($checkboxes as $name => $label)
        $output .= tarski_option_checkbox($name, $label) . "\n\n";
    
    return $output;
}

/**
 * Include an options page template fragment.
 *
 * @since 2.4
 *
 * @uses get_template_part
 *
 * @param string $block
 */
function tarski_options_fragment($block) {
    $block = preg_replace("/\.php$/", "", $block);
    get_template_part("app/templates/options/$block");
}

/**
 * Includes an options page postbox.
 *
 * @since 2.4
 *
 * @uses tarski_options_fragment
 *
 * @param string $block
 * @param string $title
 */
function tarski_options_block($block, $title) {
    echo "<div class=\"postbox\"><h3 class=\"hndle\">$title</h3>\n\t<div class=\"inside\">";
    tarski_options_fragment($block);
    echo "\t</div>\n</div>";
}

/**
 * Includes an options page postbox.
 *
 * @since 2.4
 *
 * @param string $block
 * @param string $title
 */
function tarski_options_fn_block($fn, $title, $args = array()) {
    $fn_output = call_user_func_array($fn, $args);
    
    if ($fn_output) {
        printf("<div class=\"postbox\"><h3 class=\"hndle\">%s</h3>\n\t<div class=\"inside\">%s\t</div>\n</div>",
            $title, $fn_output);
    }
}

/**
 * Returns checkbox markup for a given Tarski option.
 *
 * @since 2.4
 *
 * @param string $name
 * @param string $label
 * @return string
 */
function tarski_option_checkbox($name, $label) {
    $id      = "tarski_option_$name";
    $checked = '';
    
    if (get_tarski_option($name))
        $checked = 'checked="checked" ';
    
    $hidden   = "<input type=\"hidden\" name=\"$name\" value=\"0\" />";
    $checkbox = "<input type=\"checkbox\" id=\"$id\" name=\"$name\" value=\"1\" $checked/>";
    
    return sprintf("<label for=\"%s\">\n\t%s\n\t%s\n\t%s\n</label>",
        $id, $hidden, $checkbox, $label);
}

?>