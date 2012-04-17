<?php

/**
 * class TarskiOptions
 *
 * The TarskiOptions class handles the retrieval and setting of Tarski's
 * options, although an external function, save_tarski_options(), saves updated
 * options to the database (via WordPress's API functions). Options can be
 * set on the Tarski Options page, which can be found in the WP admin panel:
 * Presentation > Tarski Options, or /wp-admin/themes.php?page=tarski-options
 * in your WordPress directory.
 *
 * @package Tarski
 * @since 2.0
 */
class TarskiOptions {
    
    var $defaults;
    
    var $installed;
    var $deleted;
    
    var $update_notification;
    var $sidebar_pp_type;
    var $header;
    var $display_title;
    var $display_tagline;
    var $nav_pages;
    var $collapsed_pages;
    var $home_link_name;
    var $nav_extlinkcat;
    var $style;
    var $asidescategory;
    var $centred_theme;
    var $swap_sides;
    var $swap_title_order;
    var $featured_header;
    var $tags_everywhere;
    var $show_categories;
    var $show_authors;
    var $use_pages;
    
    /**
     * The TarskiOptions constructor sets all fields to their default values.
     *
     * @since 2.5
     */
    function TarskiOptions() {
        $this->set_defaults();
    }
    
    /**
     * Unserialising a TarskiOptions object also sets the defaults.
     *
     * Setting object defaults is done on wakeup in case any fields are missing
     * values, as new fields may have been added between versions.
     *
     * @since 2.5
     */
    function __wakeup() {
        $this->set_defaults();
    }
    
    /**
     * Set the default values for Tarski's options.
     *
     * This has to be performed as an method call rather than set in the class
     * definition since only constant initialisers are permitted in PHP 4.
     *
     * @since 2.5
     */
    function set_defaults() {
        $this->defaults = array(
            'installed'           => wp_get_theme()->Version,
            'update_notification' => true,
            'header'              => 'greytree.jpg',
            'display_title'       => true,
            'display_tagline'     => true,
            'nav_pages'           => false,
            'collapsed_pages'     => '',
            'home_link_name'      => __('Home', 'tarski'),
            'nav_extlinkcat'      => 0,
            'style'               => false,
            'asidescategory'      => 0,
            'centred_theme'       => true,
            'swap_sides'          => false,
            'swap_title_order'    => false,
            'featured_header'     => false,
            'tags_everywhere'     => true,
            'show_categories'     => true,
            'show_authors'        => true,
            'use_pages'           => true,
            'sidebar_pp_type'     => '');
        
        foreach ($this->defaults as $key => $value) {
            if (!isset($this->$key)) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * Sets TarskiOptions properties to the values set on the Options page.
     *
     * Note that this function doesn't save anything to the database, that's the
     * preserve of save_tarski_options().
     *
     * @since 2.0
     * @see save_tarski_options()
     */
    function tarski_options_update() {
        if (isset($_POST['alternate_style'])) {
            $style = array_slice(preg_split('/\//', $_POST['alternate_style']), 0, 2);
            $this->style = count($style) > 1 && is_valid_tarski_style($style[1]) ? $style : false;
        }
        
        if (isset($_POST['sidebar_pp_type']) && $_POST['sidebar_pp_type'] == "main") {
            $this->sidebar_pp_type = "main";
        } else {
            $this->sidebar_pp_type = "";
        }
        
        $this->display_title    = (bool) $_POST['display_title'];
        $this->display_tagline  = (bool) $_POST['display_tagline'];
        $this->show_categories  = (bool) $_POST['show_categories'];
        $this->tags_everywhere  = (bool) $_POST['tags_everywhere'];
        $this->centred_theme    = (bool) $_POST['centred_theme'];
        $this->swap_sides       = (bool) $_POST['swap_sides'];
        $this->swap_title_order = (bool) $_POST['swap_title_order'];
        $this->featured_header  = (bool) $_POST['featured_header'];
        
        unset($this->deleted);
    }
}

/**
 * Flushes Tarski's options for use by the theme.
 *
 * Creates a new TarskiOptions object, and gets the current options. If
 * no options have been set in the database, it will return the
 * defaults. Additionally, if the 'deleted' property has been set
 * then the function will check to see if it was set more than two
 * hours ago--if it was, the tarski_options database row will be
 * dropped. If the 'deleted' property has been set, then the defaults
 * will be returned regardless of whether other options are set.
 *
 * @since 1.4
 *
 * @global object $tarski_options
 * @return object $tarski_options
 */
function flush_tarski_options() {
    global $tarski_options;
    
    $opts = get_option('tarski_options');
    
    if (is_a($opts, 'TarskiOptions')) {
        $tarski_options = $opts;
    } else {
        $tarski_options = new TarskiOptions();
        
        // Upgrade from older versions of Tarski
        if (is_a($opts, 'Options')) {
            foreach ($opts->defaults as $key => $value)
                if (isset($tarski_options->defaults[$key]))
                    $tarski_options->$key = $value;
        }
    }
    
    return $tarski_options;
}

/**
 * Updates the given Tarski option with a new value.
 *
 * This function can be used either to update a particular option with a new
 * value, or to delete that option altogether by setting $value to null.
 *
 * @since 1.4
 *
 * @param string $option
 * @param mixed $value
 */
function update_tarski_option($option, $value) {
    $tarski_options = flush_tarski_options();
    
    if (is_null($value))
        unset($tarski_options->$option);
    else
        $tarski_options->$option = $value;
    
    update_option('tarski_options', $tarski_options);
}

/**
 * Returns the given Tarski option.
 *
 * @since 1.4
 *
 * @uses get_raw_tarski_option
 *
 * @param string $name
 * @return mixed
 */
function get_tarski_option($name) {
    global $tarski_options;
    
    $opt = get_raw_tarski_option($name);
    
    return is_numeric($tarski_options->deleted)
        ? $tarski_options->defaults[$name]
        : $opt;
}

/**
 * Returns the raw value of a given Tarski option.
 *
 * This function is required because, depending on the circumstances (such as
 * the TarskiOptions object being in a 'deleted' state), get_tarski_option may
 * return a default value rather than the raw value.
 *
 * @since 2.5
 *
 * @see get_tarski_option
 *
 * @global object $tarski_options
 * @param string $name
 * @return mixed
 */
function get_raw_tarski_option($name) {
    global $tarski_options;
    
    if (!is_a($tarski_options, 'TarskiOptions'))
        flush_tarski_options();
    
    return $tarski_options->$name;
}

?>