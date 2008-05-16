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
 * @package Tarski
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
	 * @return string $atomdata
	 */
	function version_feed_data() {
		ob_start();
		
		// Thanks to Simon Willison for the inspiration
		$cachefile = TARSKICACHE . "/version.atom";
		$cachetime = 60 * 60;

		// Serve from the cache if it is younger than $cachetime
		if(file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile)) && file_get_contents($cachefile)) {
			$atomdata = file_get_contents($cachefile);
		} else {
			if(function_exists('curl_init')) { // If libcurl is installed, use that
				$ch = curl_init(TARSKIVERSIONFILE);
				curl_setopt($ch, CURLOPT_FAILONERROR, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$atomdata = curl_exec($ch);
				curl_close($ch);
			} elseif(ini_get('allow_url_fopen')) { // Otherwise try file_get_contents()
				$atomdata = file_get_contents('http://tarskitheme.com/version.atom');
			}

			if(!empty($atomdata) && cache_is_writable("version.atom")) {
				$fp = fopen($cachefile, "w");
				if($fp) {
					fwrite($fp, $atomdata);
					fclose($fp);
				}
			}
		}
		
		return $atomdata;
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
		if(preg_match('/<entry>.*?<title>(.+?)<\/title>.*?<\/entry>/is', Version::version_feed_data(), $matches)) {
			$this->latest = wp_specialchars($matches[1]);
		}
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
		if(preg_match('/<entry>.*?<id>(.+?)<\/id>.*?<\/entry>/is', Version::version_feed_data(), $matches)) {
			$this->latest_link = wp_specialchars($matches[1]);
		}
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
		
		$current_version = version_to_integer($this->current);
		$latest_version = version_to_integer($this->latest);

		if($current_version === $latest_version) {
			$version_status = 'current';
		} elseif($current_version < $latest_version) {
			$version_status = 'older';
		} elseif($current_version > $latest_version) {
				$version_status = 'newer';
		} else {
			$version_status = 'no_connection';
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
function tarski_update_notifier($messages) {
	global $plugin_page;
	
	if ( !is_array($messages) )
		$messages = array();
	
	$version = new Version;
	$version->current_version_number();
	$svn_link = 'http://tarskitheme.com/help/updates/svn/';
	
	// Update checking only performed when remote files can be accessed
	if ( can_get_remote() ) {
		
		// Only performs the update check when notification is enabled
		if ( get_tarski_option('update_notification') ) {
			$version->latest_version_number();
			$version->latest_version_link();
			$version->version_status();
			
			if ( $version->status == 'older' ) {
				$messages[] = sprintf(
					__('A new version of the Tarski theme, version %1$s %2$s. Your installed version is %3$s.','tarski'),
					"<strong>$version->latest</strong>",
					'<a href="' . $version->latest_link . '">' . __('is now available','tarski') . '</a>',
					"<strong>$version->current</strong>"
				);
			} elseif ( $plugin_page == 'tarski-options' ) {
				switch($version->status) {
					case 'current':
						$messages[] = sprintf(
							__('Your version of Tarski (%s) is up to date.','tarski'),
							"<strong>$version->current</strong>"
						);
					break;
					case 'newer':
						$messages[] = sprintf(
							__('You appear to be running a development version of Tarski (%1$s). Please ensure you %2$s.','tarski'),
							"<strong>$version->current</strong>",
							"<a href=\"$svn_link\">" . __('stay updated','tarski') . '</a>'
						);
					break;
					case 'no_connection':
						$messages[] = sprintf(
							__('No connection to update server. Your installed version is %s.','tarski'),
							"<strong>$version->current</strong>"
						);
					break;
				}
			}
		} elseif ( $plugin_page == 'tarski-options' ) {
			$messages[] = sprintf(
				__('Update notification for Tarski is disabled. Your installed version is %s.','tarski'),
				"<strong>$version->current</strong>"
			);
		}
	}
	
	return $messages;
}

?>