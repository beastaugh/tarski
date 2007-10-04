<?php

class Tarski {
	
	function engage() {
		load_theme_textdomain('tarski');
		
//		$tarski_options = new Options();
		
		Tarski::require_helpers();
		Tarski::require_classes();
		
		Header::deploy();
	}
	
	function require_classes() {
		include(TARSKICLASSES."/options.php");
		include(TARSKICLASSES."/version.php");
		include(TARSKICLASSES."/header.php");
	}
	
	function require_helpers() {
		require_once(TARSKIHELPERS."/template.php");
		require_once(TARSKIHELPERS."/content.php");
		require_once(TARSKIHELPERS."/author.php");
		require_once(TARSKIHELPERS."/hooks.php");
		require_once(TARSKIHELPERS."/constants.php");
		require_once(TARSKIHELPERS."/options.php");
		require_once(TARSKIHELPERS."/upgrade.php");
		require_once(TARSKIHELPERS."/widgets.php");
		require_once(TARSKIHELPERS."/cache.php");
	}
	
}

?>