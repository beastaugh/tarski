<?php

class Version {
	
	// Class properties
	var $current;
	var $latest;
	var $latest_link;
	var $status;
	
	// Returns current version number
	function current_version_number() {
		$themedata = get_theme_data(TEMPLATEPATH . '/style.css');
		$installed_version = trim($themedata['Version']);
		if($installed_version == false) {
			$this->current = "unknown";
		} else {
			$this->current = $installed_version;
		}
	}
	
	// Returns latest version feed data
	function version_feed_data() {
		
		ob_start();
		
			require_once(TEMPLATEPATH . '/library/includes/feedparser/lib-feedparser.php');
			require_once(TEMPLATEPATH . '/library/includes/feedparser/lib-entity.php');
			require_once(TEMPLATEPATH . '/library/includes/feedparser/lib-utf8.php');
		
			// Thanks to Simon Willison for the inspiration
			$cachefile = TARSKICACHE . '/version.atom';
			$cachetime = 60 * 60;

			$parser = new FeedParserURL();
		
			// Serve from the cache if it is younger than $cachetime
			if (
				file_exists($cachefile)
				&& (time() - $cachetime < filemtime($cachefile))
				&& !(file_get_contents($cachefile) == "")
			) {
				return $parser->Parse($cachefile);
			} elseif(cache_is_writable("version.atom")) {
				$file = 'http://tarskitheme.com/version.atom';
				$ch = curl_init($file);
				$fp = @fopen($cachefile, "w");
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);

				return $parser->Parse($cachefile);
			} else {
				return $parser->Parse('http://tarskitheme.com/version.atom');
			}

		$atomdata = ob_get_contents();
		ob_end_clean();
		return $atomdata;
	}
	
	function latest_version_number() {
		$atomdata = Version::version_feed_data();
		$latest_version_number = wp_specialchars($atomdata['feed']['entries'][0]['title']['value']);
		
		$this->latest = $latest_version_number;
	}
	
	function latest_version_link() {
		$atomdata = Version::version_feed_data();
		$latest_version_link = wp_specialchars($atomdata['feed']['entries']['0']['id']);
		
		$this->latest_link = $latest_version_link;
	}
	
	function version_status() {
		$this->current_version_number();
		$this->latest_version_number();
		
		$current_version = $this->current;
		$latest_version = $this->latest;

		if(!$latest_version) {
			$version_status = "no_connection";
		} elseif($current_version == $latest_version) {
			$version_status = "current";
		} elseif($current_version != $latest_version) {
			$version_status = "not_current";
		}
		
		$this->status = $version_status;
	}
	
}

// Version controller

function tarski_update_notifier($location = "dashboard") {
	$tarski_version = new Version;
	
	$tarski_version->current_version_number();
	$tarski_version->latest_version_number();
	$tarski_version->latest_version_link();
	$tarski_version->version_status();
	
	$current = $tarski_version->current;
	$latest = $tarski_version->latest;
	$latest_link = $tarski_version->latest_link;
	$status = $tarski_version->status;
	
	if($location == "options_page") {
		include(TEMPLATEPATH."/library/partials/version/options_page.php");
	} else {
		include(TEMPLATEPATH."/library/partials/version/dashboard.php");
	}
}

if(!detectWPMU()) {
	add_action('activity_box_end', 'tarski_update_notifier');
}

?>