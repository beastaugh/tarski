<?php // Tarski update notifier

include(TEMPLATEPATH . '/library/includes/feedparser/lib-feedparser.php');
include(TEMPLATEPATH . '/library/includes/feedparser/lib-entity.php');
include(TEMPLATEPATH . '/library/includes/feedparser/lib-utf8.php');

function latest_version($option = false) {
	// Thanks to Simon Willison for the inspiration
	$cachefile = TARSKICACHE . '/version.atom';
	$cachetime = 60 * 60;

	$parser = new FeedParserURL();

	// Serve from the cache if it is younger than $cachetime
	if (file_exists($cachefile)
	&& (time() - $cachetime < filemtime($cachefile))
	&& !(file_get_contents($cachefile) == "")) {
		$atomdata = $parser->Parse($cachefile);
	} elseif(cache_is_writable("version.atom")) {
		/*
				$contents = file_get_contents('http://tarskitheme.com/version.atom');
				$fp = fopen($cachefile, 'w+');
				fwrite($fp, $contents);
				fclose($fp);
		*/
		// Above code rewritten to work with Dreamhost, where file-access is disabled in the server configuration
		$file = 'http://tarskitheme.com/version.atom';
		$ch = curl_init($file);
		$fp = @fopen($cachefile, "w");
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		
		$atomdata = $parser->Parse($cachefile);
	} else {
		$atomdata = $parser->Parse('http://tarskitheme.com/version.atom');
	}
	
	return $atomdata;
}

function latest_version_number() {
	$atomdata = latest_version();
	return wp_specialchars($atomdata['feed']['entries'][0]['title']['value']);
}

function latest_version_link() {
	$atomdata = latest_version();
	return wp_specialchars($atomdata['feed']['entries']['0']['id']);
}

function version_status() {
	if(!latest_version_number()) {
		return "noconn";
	} elseif(theme_version() == latest_version_number()) {
		return "current";
	} elseif(theme_version() != latest_version_number()) {
		return "unequal";
	}
}

function update_notifier_dashboard() {
	echo '<h3>'. __('Tarski Updates','tarski'). '</h3>'."\n";
	if(get_tarski_option('update_notification')) {
		if(version_status() == "noconn") {
			echo '<p>';
			echo __('No connection to update server. Your installed version is ','tarski'). '<strong>'. theme_version(). '</strong>'. __('.','tarski');
			echo '</p>'."\n";
		} elseif(version_status() == "current") {
			echo '<p>';
			echo __('Your version of Tarski is up to date.','tarski');
			echo '</p>'."\n";
		} elseif(version_status() == "unequal") {
			echo '<div class="updated">'."\n";
			echo '<p>';
			echo __('A new version of the Tarski theme, version ','tarski'). '<strong>'. latest_version_number(). '</strong>'. __(', ','tarski'). '<a href="'. latest_version_link(). '">'. __('is now available','tarski'). '</a>'. __('. Your installed version is ','tarski'). '<strong>'. theme_version(). '</strong>'. __('.','tarski');
			echo '</p>'."\n";
			echo '</div>'."\n";
		}
	} else {
		echo '<p>';
		echo __('Update notification for Tarski is disabled. You can enable it on the ','tarski').
			 '<a href="'. get_bloginfo('wpurl'). '/wp-admin/themes.php?page=tarski-options">'. __('Tarski Options page','tarski'). '</a>'. __('.','tarski');
		echo '</p>'."\n";
	}
}

function update_notifier_optionspage() {
	if((version_status() == 'unequal') && get_tarski_option('update_notification')) {
		echo '<div id="tarski_update_notification" class="updated">'."\n";
		echo '<p>';
		echo __('A new version of the Tarski theme, version ','tarski'). '<strong>'. latest_version_number(). '</strong>'. __(', ','tarski'). '<a href="'. latest_version_link(). '">'. __('is now available','tarski'). '</a>'. __('. Your installed version is ','tarski'). '<strong>'. theme_version(). '</strong>'. __('.','tarski');
		echo '</p>'."\n";
		echo '</div>'."\n";	
	}
}

// ~fin~ ?>