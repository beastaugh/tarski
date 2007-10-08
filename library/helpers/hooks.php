<?php

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

?>