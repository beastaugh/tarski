<?php // functions.php - Tarski functions library

// Path constants
define("TARSKILIB", TEMPLATEPATH . "/library");
define("TARSKICLASSES", TARSKILIB . "/classes");
define("TARSKIHELPERS", TARSKILIB . "/helpers");
define("TARSKIINCLUDES", TARSKILIB . "/include");
define("TARSKIDISPLAY", TARSKILIB . "/display");
define("TARSKICACHE", TARSKILIB . "/cache");		

include(TARSKICLASSES."/options.php");
include(TARSKICLASSES."/version.php");

// Warp speed
global $tarski_options;
flush_tarski_options();
@include(TEMPLATEPATH . "/constants.php");
load_theme_textdomain('tarski');

include(TARSKIHELPERS."/hooks.php");
include(TARSKIHELPERS."/upgrade.php");
include(TARSKIHELPERS."/header_helper.php");
include(TARSKIHELPERS."/template_helper.php");
include(TARSKIHELPERS."/content_helper.php");
include(TARSKIHELPERS."/author_helper.php");
include(TARSKIHELPERS."/constants_helper.php");
include(TARSKIHELPERS."/options_helper.php");
include(TARSKIHELPERS."/widgets_helper.php");
include(TARSKIHELPERS."/cache_helper.php");


function detectWPMU() {
	return function_exists('is_site_admin');
}

?>