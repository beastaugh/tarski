<?php

/**
 * class TarskiVersion
 * 
 * The Version class is the foundation of Tarski's update notifier.
 * A TarskiVersion object can have several properties: the current theme
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
class TarskiVersion {
	
	/**
	 * The raw version feed data.
	 * @var string
	 */
	var $feed_data;
	
	/**
	 * The version number of the currently installed theme.
	 * @var string
	 */
	var $current = false;
	
	/**
	 * The version number of the latest Tarski release.
	 * @var string
	 */
	var $latest = false;
	
	/**
	 * Link to the latest Tarski release post.
	 * @var string
	 */
	var $latest_link = false;
	
	/**
	 * Summary text of the latest Tarski release.
	 * @var string
	 */
	var $latest_summary = false;
	
	/**
	 * The status of the currently installed version.
	 * @var string
	 */
	var $status = 'unchecked';
	
	/**
	 * The messages associated with version states.
	 * @var string
	 */
	var $messages;
	
	/**
	 * TarskiVersion() - constructor for the TarskiVersion class.
	 * 
	 * @since 2.4
	 */
	function TarskiVersion() {
		$this->current_version_number();
		
		$this->messages = array(
			'unchecked' => array(
				'class' => 'disabled',
				'body' => sprintf(
					__('Update notification is disabled, so no attempt was made to access the update server. Your installed version is %s.', 'tarski'),
					"<strong>$this->current</strong>"
				)
			),
			'error' => array(
				'class' => 'problem',
				'body' => sprintf(
					__('An error occurred while attempting to access the update server. Your installed version is %s.', 'tarski'),
					"<strong>$this->current</strong>"
				)
			),
			'no_connection' => array(
				'class' => 'problem',
				'body' => sprintf(
					__('No connection to update server. Your installed version is %s.','tarski'),
					"<strong>$this->current</strong>"
				)
			)
		);
		
		if (get_tarski_option('update_notification')) {
			$this->version_feed_data();
			
			$this->latest_version_number();
			$this->latest_version_link();
			$this->version_status();
			$this->latest_version_summary();
			
			$this->messages = array_merge($this->messages, array(
				'current' => array(
					'class' => '',
					'body' => sprintf(
						__('Your version of Tarski (%s) is up to date.','tarski'),
						"<strong>$this->current</strong>"
					)
				),
				'older' => array(
					'class' => 'update-available',
					'body' => sprintf(
						__('Version %1$s of the Tarski theme %2$s. Your installed version is %3$s.','tarski'),
						"<strong>$this->latest</strong>",
						'<a href="' . $this->latest_link . '">' . __('is now available','tarski') . '</a>',
						"<strong>$this->current</strong>"
					) . "\n\n$this->latest_summary"
				),
				'newer' => array(
					'class' => '',
					'body' => sprintf(
						__('You appear to be running a development version of Tarski (%1$s). Please ensure you %2$s.','tarski'),
						"<strong>$this->current</strong>",
						'<a href="http://tarskitheme.com/help/updates/svn/">' . __('stay updated','tarski') . '</a>'
					)
				)
			));
		}		
	}
	
	/**
	 * current_version_number() - Returns current version number.
	 * 
	 * @since 2.0
	 */
	function current_version_number() {
		$themedata = get_theme_data(TEMPLATEPATH . '/style.css');
		$installed_version = trim($themedata['Version']);
		if(strlen($installed_version) < 1) {
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
	 * @return string
	 */
	function version_feed_data() {
		// Thanks to Simon Willison for the inspiration
		$cachefile = TARSKICACHE . "/version.atom";
		$cachetime = 60 * 60;

		// Serve from the cache if it is younger than $cachetime
		if(file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile)) && file_get_contents($cachefile)) {
			$atomdata = file_get_contents($cachefile);
		} else {
			$response = wp_remote_get(TARSKIVERSIONFILE);
			$code = wp_remote_retrieve_response_code($response);
			$atomdata = '';
			
			if (200 == $code) {
				$atomdata = wp_remote_retrieve_body($response);
				
				if (cache_is_writable("version.atom")) {
					$fp = fopen($cachefile, "w");
					if($fp) {
						fwrite($fp, $atomdata);
						fclose($fp);
					}
				}
			} else {
				$this->status = 'no_connection';
			}
		}
		
		$this->feed_data = $atomdata;
		return $this->feed_data;
	}
	
	/**
	 * latest_version_number() - Sets latest version number.
	 * 
	 * @since 2.0
	 * @return string
	 */
	function latest_version_number() {
		if(preg_match('/<entry>.*?<title>(.+?)<\/title>.*?<\/entry>/is', $this->feed_data, $matches)) {
			$this->latest = wp_specialchars($matches[1]);
		}
	}
	
	/**
	 * latest_version_link() - Sets latest version release post.
	 * 
	 * The link should be the release post on the Tarski website
	 * for the latest version of Tarski, which will include a link
	 * to download the .zip file of that latest version.
 	 * @since 2.0
	 * @return string
	 */
	function latest_version_link() {
		if(preg_match('/<entry>.*?<id>(.+?)<\/id>.*?<\/entry>/is', $this->feed_data, $matches)) {
			$this->latest_link = wp_specialchars($matches[1]);
		}
	}
	
	/**
	 * latest_version_summary() - Sets the summary text of the the latest version release post.
	 * 
	 * @since 2.4
	 * @return string
	 */
	function latest_version_summary() {
		if(preg_match('/<entry>.*?<summary>(.+?)<\/summary>.*?<\/entry>/is', $this->feed_data, $matches)) {
			$this->latest_summary = wp_specialchars($matches[1]);
		}
	}
	
	/**
	 * version_status() - Sets the status of the installed version.
	 * 
	 * This lets Tarski know whether the installed version is the current
	 * version, or whether it is older or newer than the latest version.
	 * @since 2.0
	 * @return string
	 */
	function version_status() {
		if ('no_connection' != $this->status) {
			$this->latest_version_number();
			
			if (!empty($this->latest) && !empty($this->current)) {
				$status = version_compare($this->latest, $this->current);
				
				if ($status === 0) {
					$this->status = 'current';
				} elseif ($status === 1) {
					$this->status = 'older';
				} elseif ($status === -1) {
					$this->status = 'newer';
				}
			} else {
				$this->status = 'error';
			}			
		}
	}
	
	/**
	 * status_message() - Returns an appropriate version check status message.
	 * 
	 * @since 2.4
	 */
	function status_message() {
		$message = $this->messages[$this->status];
		
		if (is_array($message) && !empty($message))
			return '<div id="tarski-update-status" class="' . $message['class'] . '">' . wpautop($message['body']) . '</div>';
	}
	
}

?>