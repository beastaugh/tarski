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

function th_sidebar() { // Sidebar hook
	do_action('th_sidebar');
}

function th_fsidebar() { // Footer sidebar hook
	do_action('th_fsidebar');
}

function th_footer() { // Footer hook
	do_action('th_footer');
}

function th_404_content() { // Error page hook
	do_action('th_404_content');
}


/* Default behaviour
--------------------------------*/

// Default document title action
add_action('th_doctitle','tarski_doctitle');

// Default header actions
add_action('th_header','tarski_headerimage');
add_action('th_header','tarski_titleandtag');

// Default navbar filters
add_filter('tarski_navbar','add_admin_link',20);

// Default content actions
add_action('th_404_content','tarski_404_default_text');

// Default sidebar actions

// Default footer actions
add_action('th_fsidebar','tarski_searchform');
add_action('th_footer','tarski_feed_and_credit');

// ~fin~ ?>