<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Tarski's constants.
 * 
 * These mostly provide convenient aliases for filesystem paths. Tarski's many
 * files live in a number of directories (the main ones being /app and
 * /library), so keeping includes simple is greatly helped by a sane set of
 * path constants. The one exception is TARSKIVERSIONFILE, which defines the
 * URL of the update notifier.
 * 
 * @see TarskiVersion
 * @link http://tarskitheme.com/help/updates/notifier/
 */
define('TARSKI_DEBUG', false);
define('TARSKICLASSES', TEMPLATEPATH . '/library/classes');
define('TARSKIHELPERS', TEMPLATEPATH . '/library/helpers');
define('TARSKIDISPLAY', TEMPLATEPATH . '/app/templates');
if (!defined('TARSKICACHE'))
	define('TARSKICACHE', WP_CONTENT_DIR . '/tarski');
if (!defined('TARSKIVERSIONFILE'))
	define('TARSKIVERSIONFILE', 'http://tarskitheme.com/version.atom');

/**
 * Core library files.
 * 
 * These files will be loaded whenever WordPress is. They include a few key
 * functions, and the core classes that Tarski requires to load its options,
 * add dependencies to document heads, and output comments.
 * 
 * @see Options
 * @see Asset
 * @see TarskiCommentWalker
 */
require_once(TEMPLATEPATH . '/library/core.php');
require_once(TARSKICLASSES . '/options.php');
require_once(TARSKICLASSES . '/asset.php');
require_once(TARSKICLASSES . '/comment_walker.php');

/**
 * Admin library files.
 * 
 * These library files are required for Tarski's administrative functions:
 * notifying the user about updates, selecting pages to add to the navbar,
 * defining the options page parameters, and so on. They are loaded only on
 * when a WordPress admin page is accessed, so as to reduce the load on the
 * server.
 * 
 * @see TarskiVersion
 * @see WalkerPageSelect
 */
if (is_admin()) {
	require_once(TARSKICLASSES . '/version.php');
	require_once(TARSKICLASSES . '/page_select.php');
	require_once(TARSKIHELPERS . '/admin_helper.php');
}

/**
 * Templating libraries.
 * 
 * As a theme, particularly given its complexity and multiplicity of options,
 * Tarski needs a lot of templating functions. There is an ongoing effort to
 * split functions up into logical groups spread across more and smaller files,
 * so that each grouping remains comprehensible and each function easy to find.
 */
require_once(TARSKIHELPERS . '/template_helper.php');
require_once(TARSKIHELPERS . '/content_helper.php');
require_once(TARSKIHELPERS . '/comments_helper.php');
require_once(TARSKIHELPERS . '/author_helper.php');
require_once(TARSKIHELPERS . '/tag_helper.php');
require_once(TARSKIHELPERS . '/widgets.php');

/**
 * API files.
 * 
 * Tarski's API is actually spread across much of the library files required
 * above, but certain pieces of functionality such as generic template hooks,
 * legacy API handlers, and deprecated functions, all live in specialised API
 * files where they can be easily found and documented.
 */
require_once(TEMPLATEPATH . '/app/api/hooks.php');
include_once(TEMPLATEPATH . '/app/api/deprecated.php');

/**
 * Launcher.
 * 
 * The launcher file makes an inital round of function calls, loading any
 * available localisation files, defining several constants which WordPress
 * requires, registering widget sidebars, and adding numerous actions and
 * filters.
 */
require_once(TEMPLATEPATH . '/app/launcher.php');

?>