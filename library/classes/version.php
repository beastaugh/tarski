<?php

/**
 * class Version
 * 
 * The Version class is the foundation of Tarski's update notifier.
 * A Version object can have several properties: the current theme
 * version (i.e. the currently installed version), the latest theme
 * version (i.e. the most recently released version, as specified by
 * the Tarski version feed {@link http://tarskitheme.com/version.atom}),
 * the link to the release post on the Tarski website of the latest
 * version, and the version status, i.e. whether the currently
 * installed version equal to the latest version, and hence whether
 * the theme is in need of updating.
 * @package tarski_version
 * @since 2.0
 */
class Version {
	
	/**
	 * The version number of the currently installed theme.
	 * @var string
	 */
	var $current;
	
	/**
	 * The version number of the latest Tarski release.
	 * @var string
	 */
	var $latest;
	
	/**
	 * Link to the latest Tarski release post.
	 * @var string
	 */
	var $latest_link;
	
	/**
	 * The status of the currently installed version.
	 * @var string
	 */
	var $status;
	
	/**
	 * current_version_number() - Returns current version number.
	 * 
	 * @since 2.0
	 */
	function current_version_number() {
		$themedata = get_theme_data(TEMPLATEPATH . '/style.css');
		$installed_version = trim($themedata['Version']);
		if($installed_version == false) {
			$this->current = 'unknown';
		} else {
			$this->current = $installed_version;
		}
	}
	
	/**
	 * version_feed_data() - Returns latest version feed data.
	 * 
	 * @link http://tarskitheme.com/version.atom
	 * @since 2.0
	 * @return array $atomdata
	 */
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
				&& file_get_contents($cachefile)
			) {
				return $parser->Parse($cachefile);
			} elseif(cache_is_writable('version.atom')) {
				$file = TARSKIVERSIONFILE;
				$ch = curl_init($file);
				$fp = @fopen($cachefile, 'w');
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);

				return $parser->Parse($cachefile);
			} else {
				return $parser->Parse(TARSKIVERSIONFILE);
			}

		$atomdata = ob_get_contents();
		ob_end_clean();
		return $atomdata;
	}
	
	/**
	 * latest_version_number() - Returns latest version number.
	 * 
	 * @since 2.0
	 * @return string
	 */
	function latest_version_number() {
		$atomdata = Version::version_feed_data();
		$latest_version_number = wp_specialchars($atomdata['feed']['entries'][0]['title']['value']);
		
		$this->latest = $latest_version_number;
	}
	
	/**
	 * latest_version_link() - Returns link to latest version release post.
	 * 
	 * The link should be the release post on the Tarski website
	 * for the latest version of Tarski, which will include a link
	 * to download the .zip file of that latest version.
 	 * @since 2.0
	 * @return string
	 */
	function latest_version_link() {
		$atomdata = Version::version_feed_data();
		$latest_version_link = wp_specialchars($atomdata['feed']['entries']['0']['id']);
		
		$this->latest_link = $latest_version_link;
	}

	/**
	 * version_status() - Returns the status of the current version.
	 * 
	 * This lets Tarski know whether there is a connection to the version
	 * feed {@link http://tarskitheme.com/version.atom} and if so, whether
	 * the current version is equal to the latest version.
	 * @since 2.0
	 * @return string
	 */
	function version_status() {
		$this->current_version_number();
		$this->latest_version_number();
		
		$current_version = $this->current;
		$latest_version = $this->latest;

		if(!$latest_version) {
			$version_status = 'no_connection';
		} elseif($current_version == $latest_version) {
			$version_status = 'current';
		} elseif($current_version != $latest_version) {
			$version_status = 'not_current';
		}
		
		$this->status = $version_status;
	}
	
}

/**
 * theme_version() - Returns either the current or the latest theme version.
 * 
 * Creates a new Version object, if {@param string $version} is
 * set to "current" then it will return the current version, if
 * set to "latest" it will return the latest version.
 * @since 2.0
 * @param string $version
 * @return string
 */
function theme_version($version = 'current') {
	$tarski_version = new Version;
	if($version == 'latest') {
		$tarski_version->latest_version_number();
		return $tarski_version->latest;
	} else {
		$tarski_version->current_version_number();
		return $tarski_version->current;
	}
}

/**
 * tarski_update_notifier() - Performs version checks and outputs the update notifier.
 * 
 * Creates a new Version object, checks the latest and current
 * versions, and lets the user know whether or not their version
 * of Tarski needs updating. The way it displays varies slightly
 * between the WordPress Dashboard and the Tarski Options page.
 * @since 2.0
 * @param string $location
 * @return string
 */
function tarski_update_notifier($location = 'dashboard') {
	
	$tarski_version = new Version;
	
	$tarski_version->current_version_number();
	
	// Only performs the update check when notification is enabled
	if(get_tarski_option('update_notification')) {
		$tarski_version->latest_version_number();
		$tarski_version->latest_version_link();
		$tarski_version->version_status();
	}
	
	$current = $tarski_version->current;
	$latest = $tarski_version->latest;
	$latest_link = $tarski_version->latest_link;
	$status = $tarski_version->status;
	
	if($location == 'options_page') {
		include(TARSKIDISPLAY . '/admin/version_options.php');
	} elseif(!detectWPMU() || detectWPMUadmin()) {
		include(TARSKIDISPLAY . '/admin/version_dashboard.php');
	}
}

?>