<?php

class Options {
	
	var $installed;
	var $debug = false;
	var $update_notification;
	var $blurb;
	var $footer_recent;
	var $sidebar_type;
	var $sidebar_onlyhome;
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
	var $centered_theme;
	var $swap_sides;
	var $tags_everywhere;
	var $show_categories;
	var $use_pages;
	var $feed_type;
	
	function tarski_options_defaults() {
		$this->installed = theme_version("current");
		$this->update_notification = true;
		$this->blurb = __("This is the about text.","tarski");
		$this->footer_recent = true;
		if(function_exists('register_sidebar')) { 
			$this->sidebar_type = "widgets";
		} else {
			$this->sidebar_type = "tarski";
		}
		$this->sidebar_onlyhome = false;
		$this->sidebar_pages = true;
		$this->sidebar_links = true;
		$this->sidebar_custom = false;
		$this->header = "greytree.jpg";
		$this->display_title = true;
		$this->display_tagline = true;
		$this->nav_pages = false;
		$this->home_link_name = __("Home","tarski");
		$this->nav_extlinkcat = 0;
		$this->style = false;
		$this->asidescategory = 0;
		$this->centered_theme = true;
		$this->swap_sides = false;
		$this->swap_title_order = false;
		$this->tags_everywhere = false;
		$this->show_categories = true;
		$this->use_pages = false;
		$this->feed_type = "rss2";
	}
	
	function tarski_options_get() {
		$array = unserialize(get_option("tarski_options"));
		if(!empty($array)) {
			foreach($array as $name => $value) {
				$this->$name = $value;
			}
		}
	}
	
	function tarski_options_update() {
		global $wpdb, $user_ID;
		get_currentuserinfo();

		if(($_POST['delete_options'] == 1)) {
			$this->deleted = time();
		} elseif($_POST['restore_options'] == 1) {
			$this->deleted = false;
		} elseif(isset($_POST['Submit'])) {
			if($_POST['update_notification'] == 'off') {
				$this->update_notification = false;
			} elseif($_POST['update_notification'] == 'on') {
				$this->update_notification = true;
			}

			if (isset($_POST['about_text'])) {
				$about = $_POST['about_text'];
				$this->blurb = $about;
			}
			if (isset($_POST['header_image'])) {
				$header = $_POST['header_image'];
				$header = @str_replace("-thumb", "", $header);
				$this->header = $header;
			}
			if(isset($_POST['nav_pages'])) {
				$nav_pages = implode(",", $_POST['nav_pages']);
			}
			
			$stylefile = $_POST['alternate_style'];
			$illegal_names = '/^janus.css$|^single.css$|^centre.css$|^left.css$/';
			if(!preg_match($illegal_names, $stylefile)) {
				$this->style = $stylefile;
			}
			
			
			$this->footer_recent = $_POST['footer']['recent'];
			$this->sidebar_pages = $_POST['sidebar']['pages'];
			$this->sidebar_links = $_POST['sidebar']['links'];
			$this->sidebar_custom = $_POST['sidebar']['custom'];
			$this->sidebar_onlyhome = $_POST['sidebar']['onlyhome'];
			$this->display_title = $_POST['display_title'];
			$this->display_tagline = $_POST['display_tagline'];
			$this->show_categories = $_POST['show_categories'];
			$this->tags_everywhere = $_POST['tags_everywhere'];
			$this->use_pages = $_POST['use_pages'];
			$this->centered_theme = $_POST['centered_theme'];
			$this->swap_sides = $_POST['swap_sides'];
			$this->swap_title_order = $_POST['swap_title_order'];
			$this->asidescategory = $_POST['asides_category'];
			$this->nav_pages = $nav_pages;
			$this->nav_extlinkcat = $_POST['nav_extlinkcat'];
			$this->home_link_name = $_POST['home_link_name'];
			$this->sidebar_type = $_POST['sidebartype'];
			$this->feed_type = $_POST['feed_type'];
		}
	}

}

function save_tarski_options() {
	if(isset($_POST['Submit'])) {
		$tarski_options = new Options;
		$tarski_options->tarski_options_get();
		$tarski_options->tarski_options_update();
		update_option('tarski_options', serialize($tarski_options));
	}
	flush_tarski_options();
}

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

function update_tarski_option($option, $value, $drop = false) {
	global $tarski_options;
	$tarski_options->$option = $value;
	if($drop == true) {
		unset($tarski_options->$name);
	}
	update_option('tarski_options', serialize($tarski_options));
	flush_tarski_options();
}

function add_tarski_option($name, $value) {
	update_tarski_option($name, $value);
}

function drop_tarski_option($option) {
	update_tarski_option($option, "", true);
}

function get_tarski_option($name) {
	global $tarski_options;
	return $tarski_options->$name;
}

function tarski_option($name) {
	echo get_tarski_option($name);
}

?>