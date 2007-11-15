<?php

/**
 * class Options
 * 
 * The Options class handles the retrieval and setting of Tarski's options,
 * although an external function, save_tarski_options(), saves updated
 * options to the database (via WordPress's API functions). Options can be
 * set on the Tarski Options page, which can be found in the WP admin panel:
 * Presentation > Tarski Options, or /wp-admin/themes.php?page=tarski-options
 * in your WordPress directory.
 * @package tarski_options
 * @since 2.0
 */
class Options {
	
	var $installed;
	var $deleted;
	var $debug = false;
	var $update_notification;
	var $blurb;
	var $footer_recent;
	var $sidebar_type;
	var $sidebar_pp_type;
	var $sidebar_pages;
	var $sidebar_links;
	var $sidebar_custom;
	var $header;
	var $display_title;
	var $display_tagline;
	var $nav_pages;
	var $home_link_name;
	var $nav_extlinkcat;
	var $style;
	var $asidescategory;
	var $centred_theme;
	var $swap_sides;
	var $tags_everywhere;
	var $show_categories;
	var $use_pages;
	var $feed_type;
	
	/**
	 * tarski_options_defaults() - Sets Options object's properties to their default values.
	 * 
	 * @since 2.0
	 */
	function tarski_options_defaults() {
		$this->installed = theme_version('current');
		$this->update_notification = true;
		$this->blurb = __('This is the default Tarski blurb: you can edit or remove it on the Tarski Options page.','tarski');
		$this->footer_recent = true;
		$this->sidebar_type = 'tarski';
		$this->sidebar_pp_type = 'main';
		$this->sidebar_pages = true;
		$this->sidebar_links = true;
		$this->sidebar_custom = false;
		$this->header = 'greytree.jpg';
		$this->display_title = true;
		$this->display_tagline = true;
		$this->nav_pages = false;
		$this->home_link_name = __('Home','tarski');
		$this->nav_extlinkcat = 0;
		$this->style = false;
		$this->asidescategory = 0;
		$this->centred_theme = true;
		$this->swap_sides = false;
		$this->swap_title_order = false;
		$this->tags_everywhere = false;
		$this->show_categories = true;
		$this->show_authors = tarski_should_show_authors();
		$this->use_pages = false;
		$this->feed_type = 'rss2';
	}
	
	/**
	 * tarski_options_get() - Sets Options properties to the values retrieved from the database.
	 * 
	 * @since 2.0
	 */
	function tarski_options_get() {
		$array = unserialize(get_option('tarski_options'));
		if(!empty($array) && is_object($array)) {
			foreach($array as $name => $value) {
				$this->$name = $value;
			}

			if(empty($this->installed) || ($this->installed != theme_version('current'))) {
				// We had some Tarski preferences, but the preferences were from a different version, so we need to update them
				
				// Get our defaults, so we can merge them in
				$defaults = new Options;
				$defaults->tarski_options_defaults();

				// Handle special cases first
				
				// Update the options version so we don't run this code more than once
				$this->installed = $defaults->installed;
				
				// If they had hidden the sidebar previously for non-index pages, preserve that setting
				if(empty($this->sidebar_pp_type) && isset($this->sidebar_onlyhome) && $this->sidebar_onlyhome == 1) {
					$this->sidebar_pp_type = 'none';
				}
				
				// If there's more than one author, show authors
				if(tarski_should_show_authors()) {
					$this->show_authors = true;
				}
				
				// If categories are hidden, respect that option
				if(empty($this->show_categories) && isset($this->hide_categories) && ($this->hide_categories == 1)) {
					$this->show_categories = false;
				}
				
				// Change American English to British English, sorry Chris
				if(empty($this->centred_theme) && isset($this->centered_theme)) {
					$this->centred_theme = true;
				}
				
				// Conform our options to the expected values, types, and defaults
				foreach($this as $name => $value) {
					if(!isset($defaults->$name)) {
						// Get rid of options which no longer exist
						unset($this->$name);
					} else if(!isset($this->$name)) {
						// Use the default if we don't have this option
						$this->$name = $defaults->$name;
					} else if(is_array($this->$name) && !is_array($defaults->$name)) {
						// If our option is an array and the default is not, implode using " " as a separator
						$this->$name = implode(" ", $this->$name);
					} else if(!is_array($this->$name) && is_array($defaults->$name)) {
						// If our option is a scalar and the default is an array, wrap our option in an array
						$this->$name = array($this->$name);
					}
				}

				// Save our updated options
				update_option('tarski_options', serialize($this));
				flush_tarski_options();
			}
		}
	}
	
	/**
	 * tarski_options_update() - Sets Options properties to the values set on the Options page.
	 * 
	 * Note that this function doesn't save anything to the database, that's the
	 * preserve of save_tarski_options().
	 * @since 2.0
	 * @see save_tarski_options()
	 */
	function tarski_options_update() {
		global $wpdb, $user_ID;
		get_currentuserinfo();

		if(($_POST['delete_options'] == 1)) {
			$this->deleted = time();
		} elseif($_POST['restore_options'] == 1) {
			$this->deleted = false;
		} else {
			if($_POST['update_notification'] == 'off')
				$this->update_notification = false;
			elseif($_POST['update_notification'] == 'on')
				$this->update_notification = true;
			
			if(isset($_POST['about_text']))
				$this->blurb = $_POST['about_text'];
			
			$header = $_POST['header_image'];
			if(isset($header)) {
				$header = @str_replace('-thumb', '', $header);
				$this->header = $header;
			}
			
			$nav_pages = $_POST['nav_pages'];
			if(isset($nav_pages)) {
				$nav_pages = implode(',', $nav_pages);
				$this->nav_pages = $nav_pages;
			} else {
				$this->nav_pages = false;
			}
			
			$stylefile = $_POST['alternate_style'];
			if(is_valid_tarski_style($stylefile))
				$this->style = $stylefile;
			elseif(!$stylefile)
				$this->style = false;
			
			$this->footer_recent = (bool) $_POST['footer']['recent'];
			$this->sidebar_pages = (bool) $_POST['sidebar']['pages'];
			$this->sidebar_links = (bool) $_POST['sidebar']['links'];
			$this->sidebar_custom = $_POST['sidebar']['custom'];
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
			$this->sidebar_type = $_POST['sidebar_type'];
			$this->sidebar_pp_type = $_POST['sidebar_pp_type'];
			$this->feed_type = $_POST['feed_type'];
			
			$this->show_authors = tarski_should_show_authors();
		}
	}

}

/**
 * save_tarski_options() - Saves a new set of Tarski options.
 * 
 * If the Tarski Options page request includes a $_POST call
 * and it's been generated by hitting the 'Submit' button, this
 * function will generate a new Options object, set its properties
 * to the existing set of options, and then save the new options
 * over the old ones. It then flushes the options so the Options
 * page, which executes after this function, will display the new
 * values rather than the old ones.
 * @see tarskiupdate() which it replaces
 * @since 2.0
 */
function save_tarski_options() {
	if(isset($_POST['Submit'])) {
		$tarski_options = new Options;
		$tarski_options->tarski_options_get();
		$tarski_options->tarski_options_update();
		update_option('tarski_options', serialize($tarski_options));
	}
	flush_tarski_options();
}

/**
 * flush_tarski_options() - Flushes Tarski's options for use by the theme.
 * 
 * Creates a new Options object, and gets the current options. If
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
	$tarski_options = new Options;
	if(get_option('tarski_options')) {
		$tarski_options->tarski_options_get();
		if(get_tarski_option('deleted')) {
			if((time() - (int) get_tarski_option('deleted')) > 2 * 60 * 60) {
				delete_option('tarski_options');
			}
			$tarski_options->tarski_options_defaults();
		}
	} else {
		$tarski_options->tarski_options_defaults();
	}
}

/**
 * update_tarski_option() - Updates the given Tarski option with a new value.
 * 
 * This function can be used either to update a particular option
 * with a new value, or to delete that option altogether by setting
 * $drop to true.
 * @since 1.4
 * @param string $option
 * @param string $value
 * @param boolean $drop
 * @global object $tarski_options
 * @return object $tarski_options
 */
function update_tarski_option($option, $value, $drop = false) {
	global $tarski_options;
	$tarski_options->$option = $value;
	
	if($drop == true)
		unset($tarski_options->$name);
	
	update_option('tarski_options', serialize($tarski_options));
	flush_tarski_options();
}

/**
 * add_tarski_option() - Adds a new Tarski option.
 * 
 * This function is just an alias for update_tarski_option(), but
 * with a more restricted set of parameters.
 * @since 1.6
 * @see update_tarski_option()
 * @param string $name
 * @param string $value
 * @return object $tarski_options
 */
function add_tarski_option($name, $value) {
	update_tarski_option($name, $value);
}

/**
 * drop_tarski_option() - Drops the given Tarski option.
 * 
 * This function is just an alias for update_tarski_option(), but
 * with a more restricted set of parameters.
 * @since 1.6
 * @see update_tarski_option()
 * @param string $name
 * @return object $tarski_options
 */
function drop_tarski_option($option) {
	update_tarski_option($option, false, true);
}

/**
 * get_tarski_option() - Returns the given Tarski option.
 * 
 * @since 1.4
 * @param string $name
 * @return mixed
 */
function get_tarski_option($name) {
	global $tarski_options;
	return $tarski_options->$name;
}

/**
 * tarski_option() - Outputs the given Tarski option.
 * 
 * Basically just echoes the value returned by the complementary
 * function get_tarski_option().
 * @since 1.4
 * @see get_tarski_option()
 */
function tarski_option($name) {
	echo get_tarski_option($name);
}

?>