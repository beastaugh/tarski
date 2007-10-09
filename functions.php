<?php

// Path constants
define("TARSKILIB", TEMPLATEPATH . "/library");
define("TARSKICLASSES", TARSKILIB . "/classes");
define("TARSKIHELPERS", TARSKILIB . "/helpers");
define("TARSKIDISPLAY", TARSKILIB . "/display");
define("TARSKICACHE", TARSKILIB . "/cache");
define("TARSKIVERSIONFILE", "http://tarskitheme.com/version.atom");		

// Classes
require_once(TARSKICLASSES."/options.php");
require_once(TARSKICLASSES."/version.php");

// Helpers
include(TARSKIHELPERS."/hooks.php");
include(TARSKIHELPERS."/admin_helper.php");
include(TARSKIHELPERS."/template_helper.php");
include(TARSKIHELPERS."/content_helper.php");
include(TARSKIHELPERS."/author_helper.php");
include(TARSKIHELPERS."/constants_helper.php");
include(TARSKIHELPERS."/cache_helper.php");

// Options
load_theme_textdomain("tarski");
global $tarski_options;
flush_tarski_options();
@include(TEMPLATEPATH . "/constants.php");

// Launch
require_once(TARSKILIB . "/launcher.php");

?>