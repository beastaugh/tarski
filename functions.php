<?php

// Path constants
define('TARSKILIB', TEMPLATEPATH . '/library');
define('TARSKICLASSES', TARSKILIB . '/classes');
define('TARSKIHELPERS', TARSKILIB . '/helpers');
define('TARSKIDISPLAY', TARSKILIB . '/display');
define('TARSKIWIDGETS', TARSKILIB . '/widgets');
define('TARSKICACHE', TARSKILIB . '/cache');
define('TARSKIVERSIONFILE', 'http://tarskitheme.com/version.atom');

// Classes
require_once(TARSKICLASSES . '/options.php');
require_once(TARSKICLASSES . '/version.php');
require_once(TARSKICLASSES . '/page_select.php');
require_once(TARSKICLASSES . '/asset.php');

// Helpers
require_once(TARSKIHELPERS . '/hooks.php');
require_once(TARSKIHELPERS . '/admin_helper.php');
require_once(TARSKIHELPERS . '/template_helper.php');
require_once(TARSKIHELPERS . '/content_helper.php');
require_once(TARSKIHELPERS . '/author_helper.php');
require_once(TARSKIHELPERS . '/tag_helper.php');

// Widgets
require_once(TARSKIHELPERS . '/widgets.php');

// Deprecated
require_once(TARSKIHELPERS . '/constants_helper.php');
include_once(TARSKIHELPERS . '/deprecated.php');

// Launch
require_once(TARSKILIB . '/launcher.php');

?>