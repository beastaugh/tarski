<?php

// Path constants
define('TARSKICLASSES', TEMPLATEPATH . '/library/classes');
define('TARSKIHELPERS', TEMPLATEPATH . '/library/helpers');
define('TARSKIDISPLAY', TEMPLATEPATH . '/app/display');
define('TARSKICACHE', TEMPLATEPATH . '/app/cache');
define('TARSKIVERSIONFILE', 'http://tarskitheme.com/version.atom');

// Core library files
require_once(TEMPLATEPATH . '/library/core.php');
require_once(TARSKICLASSES . '/options.php');
require_once(TARSKICLASSES . '/asset.php');

// Admin library files
if (is_admin()) {
	require_once(TARSKICLASSES . '/version.php');
	require_once(TARSKICLASSES . '/page_select.php');
	require_once(TARSKIHELPERS . '/admin_helper.php');
}

// Various helper libraries
require_once(TARSKIHELPERS . '/template_helper.php');
require_once(TARSKIHELPERS . '/content_helper.php');
require_once(TARSKIHELPERS . '/author_helper.php');
require_once(TARSKIHELPERS . '/tag_helper.php');
require_once(TARSKIHELPERS . '/widgets.php');

// API files
require_once(TEMPLATEPATH . '/app/api/hooks.php');
require_once(TEMPLATEPATH . '/app/api/constants_helper.php');
include_once(TEMPLATEPATH . '/app/api/deprecated.php');

// Launch
require_once(TEMPLATEPATH . '/app/launcher.php');

?>