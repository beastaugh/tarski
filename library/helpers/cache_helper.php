<?php

/**
 * Checks if WordPress can write to $file in Tarski's cache directory.
 * 
 * If $file isn't given, the function checks to see if new files can 
 * be written to the cache directory.
 */
function cache_is_writable($file = false) {
	if($file == "") {
		$cachefile = false;
	} else {
		$cachefile = TARSKICACHE. "/". $file;
	}
	if(
		is_writable($cachefile)
		|| (is_writable(TARSKICACHE) && !file_exists($cachefile))
	) {
		return true;
	}
}

?>