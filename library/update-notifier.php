<?php // Tarski update notifier

include(TEMPLATEPATH . '/library/feedparser/lib-feedparser.php');
include(TEMPLATEPATH . '/library/feedparser/lib-entity.php');
include(TEMPLATEPATH . '/library/feedparser/lib-utf8.php');

function latest_version($option = false) {
	$parser = new FeedParserURL();
	$result = $parser->Parse('http://tarskitheme.com/version.atom');
	
	if($option == 'link') {
		return wp_specialchars($result['feed']['entries'][0]['id']);
	} else {
		return wp_specialchars($result['feed']['entries'][0]['title']['value']);
	}
}

function version_status() {
	if(!latest_version()) {
		return "noconn";
	} elseif(theme_version() == latest_version()) {
		return "current";
	} elseif(theme_version() != latest_version()) {
		return "unequal";
	}
}

function update_notifier_dashboard() {
	echo '<h3>'. __('Tarski Updates','tarski'). '</h3>'."\n";
	echo '<p>';
	if(get_tarski_option('update_notification') == 'true') {
		if(version_status() == "noconn") {
			echo __('No connection to update server. Your installed version is ','tarski'). '<strong>'. theme_version(). '</strong>'. __('.','tarski');
		} elseif(version_status() == "current") {
			echo __('Your version of Tarski is up to date.','tarski');
		} elseif(version_status() == "unequal") {
			echo __('A new version of the Tarski theme, version ','tarski'). '<strong>'. latest_version(). '</strong>'. __(', ','tarski'). '<a href="'. latest_version('link'). '">'. __('is now available','tarski'). '</a>'. __('. Your installed version is ','tarski'). '<strong>'. theme_version(). '</strong>'. __('.','tarski');
		}
	} else {
		echo __('Update notification for Tarski is disabled. You can enable it on the ','tarski').
			 '<a href="'. get_bloginfo('wpurl'). '/wp-admin/themes.php?page=tarski-options">'. __('Tarski Options page','tarski'). '</a>'. __('.','tarski');
	}
	echo '</p>'."\n";
}

function update_notifier_optionspage() {
	if((version_status() == 'unequal') && (get_tarski_option('update_notification') == 'true')) {
		echo '<div id="tarski_update_notification" class="updated">'."\n";
		echo '<p>';
		echo __('A new version of the Tarski theme, version ','tarski'). '<strong>'. latest_version(). '</strong>'. __(', ','tarski'). '<a href="'. latest_version('link'). '">'. __('is now available','tarski'). '</a>'. __('. Your installed version is ','tarski'). '<strong>'. theme_version(). '</strong>'. __('.','tarski');
		echo '</p>'."\n";
		echo '</div>'."\n";	
	}
}

// ~fin~ ?>