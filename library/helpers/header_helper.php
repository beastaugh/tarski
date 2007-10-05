<?php

if(function_exists('add_custom_image_header')) {
	tarski_config_custom_header();
	add_custom_image_header('', 'tarski_admin_header_style');
}

function tarski_config_custom_header() {
	define('HEADER_TEXTCOLOR', '');
	define('HEADER_IMAGE', '%s/headers/' . get_tarski_option('header'));
	// %s is theme dir uri
	define('HEADER_IMAGE_WIDTH', 720);
	define('HEADER_IMAGE_HEIGHT', 180);
	define('NO_HEADER_TEXT', true );
}

function tarski_admin_header_style() {
	include(TARSKIDISPLAY."/options/admin_header_style.php");
}

?>