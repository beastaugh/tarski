<?php

// Can we write to the cache?
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