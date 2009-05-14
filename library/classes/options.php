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
	var $tags_everywhere;
	var $show_categories;
	var $show_authors;
	var $use_pages;
	
	/**
	 * Sets TarskiOptions object's properties to their default values.
	 *
	 * @since 2.0
	 */
	function tarski_options_defaults() {
		$this->installed = theme_version('current');
		$this->update_notification = true;
		$this->sidebar_pp_type = 'main';
		$this->header = 'greytree.jpg';
		$this->display_title = true;
		$this->display_tagline = true;
		$this->nav_pages = false;
		$this->collapsed_pages = '';
		$this->home_link_name = __('Home', 'tarski');
		$this->nav_extlinkcat = 0;
		$this->style = false;
		$this->asidescategory = 0;
		$this->centred_theme = true;
		$this->swap_sides = false;
		$this->swap_title_order = false;
		$this->tags_everywhere = true;
		$this->show_categories = true;
		$this->show_authors = true;
		$this->use_pages = true;
	}
	
	/**
	 * Sets TarskiOptions properties to the values retrieved from the database.
	 *
	 * @since 2.0
	 */
	function tarski_options_get() {
		$saved_options = maybe_unserialize(get_option('tarski_options'));
		
		if (empty($saved_options)) return;
		
		foreach ($saved_options as $name => $value) {
			if ((function_exists('property_exists') &&
			property_exists($this, 'installed')) ||
			array_key_exists('installed', $this)) {
				$this->$name = $value;
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
		if (isset($_POST['update_notification'])) {
			if ($_POST['update_notification'] == 'off')
				$this->update_notification = false;
			elseif ($_POST['update_notification'] == 'on')
				$this->update_notification = true;
		}
		
		if (isset($_POST['header_image']))
			$this->header = str_replace('-thumb', '', $_POST['header_image']);
		
		if (isset($_POST['nav_pages']))
			$this->nav_pages = implode(',', $_POST['nav_pages']);
		else
			$this->nav_pages = false;
		
		if (isset($_POST['collapsed_pages']))
			$this->collapsed_pages = $_POST['collapsed_pages'];
		else
			$this->collapsed_pages = '';
		
		if (isset($_POST['alternate_style'])) {
			$stylefile = $_POST['alternate_style'];
			if (is_valid_tarski_style($stylefile))
				$this->style = $stylefile;
			else
				$this->style = false;
		}
		
		$this->display_title = (bool) $_POST['display_title'];
		$this->display_tagline = (bool) $_POST['display_tagline'];
		$this->show_categories = (bool) $_POST['show_categories'];
		$this->tags_everywhere = (bool) $_POST['tags_everywhere'];
		$this->use_pages = (bool) $_POST['use_pages'];
		$this->centred_theme = (bool) $_POST['centred_theme'];
		$this->swap_sides = (bool) $_POST['swap_sides'];
		$this->swap_title_order = (bool) $_POST['swap_title_order'];
		$this->asidescategory = $_POST['asides_category'];
		$this->nav_extlinkcat = $_POST['nav_extlinkcat'];
		$this->home_link_name = $_POST['home_link_name'];
		$this->sidebar_pp_type = $_POST['sidebar_pp_type'];			
		$this->show_authors = tarski_should_show_authors();
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
 * @since 1.4
 * @global object $tarski_options
 * @return object $tarski_options
 */
function flush_tarski_options() {
	global $tarski_options;
	
	$tarski_options = new TarskiOptions;
	$tarski_options->tarski_options_get();
	
	if (!get_option('tarski_options') || isset($tarski_options->deleted))
		$tarski_options->tarski_options_defaults();
}

/**
 * Updates the given Tarski option with a new value.
 *
 * This function can be used either to update a particular option with a new
 * value, or to delete that option altogether by setting $drop to true.
 *
 * @since 1.4
 *
 * @param string $option
 * @param string $value
 * @param boolean $drop
 * @global object $tarski_options
 */
function update_tarski_option($option, $value) {
	$tarski_options = new TarskiOptions;
	$tarski_options->tarski_options_get();
		
	if (empty($value))
		unset($tarski_options->$option);
	else
		$tarski_options->$option = $value;
		
	update_option('tarski_options', $tarski_options);
	flush_tarski_options();
}

/**
 * Returns the given Tarski option.
 * 
 * @since 1.4
 *
 * @param string $name
 * @return mixed
 */
function get_tarski_option($name) {
	global $tarski_options;
	
	if (!is_object($tarski_options))
		flush_tarski_options();
	
	return $tarski_options->$name;
}

?>