<?php

// Path constants
define('TARSKILIB', TEMPLATEPATH . '/library');
define('TARSKICLASSES', TARSKILIB . '/classes');
define('TARSKIHELPERS', TARSKILIB . '/helpers');
define('TARSKIDISPLAY', TARSKILIB . '/display');
define('TARSKICACHE', TARSKILIB . '/cache');
define('TARSKIVERSIONFILE', 'http://tarskitheme.com/version.atom');

// Classes
require_once(TARSKICLASSES . '/options.php');
require_once(TARSKICLASSES . '/version.php');

// Helpers
include_once(TARSKIHELPERS . '/hooks.php');
include_once(TARSKIHELPERS . '/admin_helper.php');
include_once(TARSKIHELPERS . '/template_helper.php');
include_once(TARSKIHELPERS . '/content_helper.php');
include_once(TARSKIHELPERS . '/page_template_helper.php');
include_once(TARSKIHELPERS . '/author_helper.php');
include_once(TARSKIHELPERS . '/tag_helper.php');
include_once(TARSKIHELPERS . '/constants_helper.php');
include_once(TARSKIHELPERS . '/cache_helper.php');
include_once(TARSKIHELPERS . '/deprecated.php');

// Options
load_theme_textdomain('tarski');
global $tarski_options;
flush_tarski_options();
@include(TEMPLATEPATH . '/constants.php');

// Custom header constants
define('HEADER_TEXTCOLOR', '');
define('HEADER_IMAGE', '%s/headers/' . get_tarski_option('header')); // %s is theme directory URI
define('HEADER_IMAGE_WIDTH', 720);
define('HEADER_IMAGE_HEIGHT', 180);
define('NO_HEADER_TEXT', true);

// Launch
require_once(TARSKILIB . '/launcher.php');

?>