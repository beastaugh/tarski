<?php // constants-hooks.php - Tying Tarski's hooks into the legacy constants file

// General constants check
function tarski_output_constant($constant) {
	if($constant) {
		echo $constant;
	}
}

// Output $headerInclude from constants file
function tarski_output_headinclude() {
	global $headerInclude;
	tarski_output_constant($headerInclude);
}

// Output $frontPageInclude from constants file
function tarski_output_frontpageinclude() {
	global $frontPageInclude;
	global $completedBlurb;
	
	if(is_home() && !$completedBlurb) {
		$completedBlurb = 1;
		tarski_output_constant($frontPageInclude);
	}
}

// Output $postEndInclude from constants file
function tarski_output_postendinclude() {
	global $postEndInclude;
	tarski_output_constant($postEndInclude);
}

// Output $pageEndInclude from constants file
function tarski_output_pageendinclude() {
	global $pageEndInclude;
	tarski_output_constant($pageEndInclude);
}

// Output $footerInclude from constants file
function tarski_output_footerinclude() {
	global $footerInclude;
	if($footerInclude) { ?>
		<div id="footer-include">
			<?php echo $footerInclude; ?>
		</div>
<?php }
}

// Output $archivesPageInclude from constants file
function tarski_output_archivesinclude() {
	global $archivesPageInclude;
	tarski_output_constant($archivesPageInclude);
}

// Output $errorPageInclude from constants file
function tarski_output_errorinclude() {
	global $errorPageInclude;
	if($errorPageInclude) {
		echo $errorPageInclude;
	} else {
		echo '<p>' . __('The page you are looking for does not exist; it may have been moved, or removed altogether. You might want to try the search function or return to the ','tarski') . '<a href="' . get_settings('home') . '">' . __('front page','tarski') . '</a>' . __('.','tarski') . "</p>\n";
	}
}

// Default header action
add_action('wp_head','tarski_output_headinclude');
// Default front page action
add_action('th_postend','tarski_output_frontpageinclude');
// Default (single) post end action
add_action('th_singleend','tarski_output_postendinclude');
// Default page end action
add_action('th_pageend','tarski_output_pageendinclude');
// Default footer action
add_action('th_footer','tarski_output_footerinclude');
// Default archives page action
add_action('th_archside','tarski_output_archivesinclude');
// Default error page action
add_action('th_404','tarski_output_errorinclude');

// ~fin~ ?>