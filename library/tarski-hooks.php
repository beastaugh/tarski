<?php // tarski-hooks.php - Tarski hooks and their default behaviour

/* Tarski theme hooks
--------------------------------*/

function th_doctitle() { // Document title hook
	do_action('th_doctitle');
}

function th_header() { // Header image hook
	do_action('th_header');
}

function th_postend() { // Post end hook
	do_action('th_postend');
}

function th_singleend() { // Single post end hook
	do_action('th_singleend');
}

function th_pageend() { // Page end hook
	do_action('th_pageend');
}

function th_footer() { // Archives sidebar hook
	do_action('th_footer');
}

function th_archside() { // Archives sidebar hook
	do_action('th_archside');
}

function th_404() { // Error page hook
	do_action('th_404');
}


/* Default behaviour
--------------------------------*/

// Default document title action
add_action('th_doctitle','tarski_doctitle');

// Default header actions
add_action('th_header','tarski_headerimage');
add_action('th_header','tarski_titleandtag');

// Default footer actions
add_action('th_footer','tarski_feed_and_credit');

// ~fin~ ?>