<?php // tarski-hooks.php - Tarski hooks and their default behaviour


/* Actions
--------------------------------*/

function th_doctitle() { // Document title hook
	do_action('th_doctitle');
}

function th_header() { // Header hook
	do_action('th_header');
}

function th_navbar() { // Navbar hook
	do_action('th_navbar');
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


/* Filters
--------------------------------*/

/*
'th_navbar'
See /library/template-functions.php

'tarski_navbar'
See /library/template-functions.php

'tarski_404_content'
See /library/content-functions.php

'tarski_footer_blurb'
See /library/content-functions.php

I.e., add_filter('tarski_404_content','my_function');
*/


/* Default behaviour
--------------------------------*/

// Default document title action
add_action('th_doctitle','tarski_doctitle');

// Default header actions
add_action('th_header','tarski_headerimage');
add_action('th_header','tarski_titleandtag');

// Default navbar actions and filters
add_filter('tarski_navbar','add_admin_link',20);
add_filter('tarski_navbar','wrap_navlist',21);
add_action('th_navbar','tarski_navbar');
add_action('th_navbar','tarski_navbar_feedlink');

// Default content actions
add_action('th_postend','add_post_tags');
add_action('th_postend','link_pages_without_spaces');

// Default sidebar actions

// Default footer actions
add_action('th_fsidebar','tarski_searchform');
add_action('th_footer','tarski_feed_and_credit');


// ~fin~ ?>