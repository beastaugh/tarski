<?php

/**
 * is_active_sidebar() - Checks to see whether a particular sidebar has widgets.
 * 
 * Stolen from ticket #4594 on Trac, hence the conditional definition.
 * @link http://trac.wordpress.org/ticket/4594
 * @since 2.0
 * @return boolean
 */
if(!function_exists('is_active_sidebar')) {
	function is_active_sidebar( $index ) {
		$index = ( is_int($index) ) ? "sidebar-$index" : sanitize_title($index);
		$sidebars_widgets = (array) get_option('sidebars_widgets');	
		return (bool) ( isset( $sidebars_widgets[$index] ) );
	}
}

/**
 * tarski_footer_sidebar() - Outputs footer main widgets field.
 * 
 * @since 2.1
 * @return mixed
 */
function tarski_footer_main() {
	dynamic_sidebar('footer-main');
}

/**
 * tarski_footer_sidebar() - Outputs the footer sidebar widgets field.
 * 
 * @since 2.0
 * @return mixed
 */
function tarski_footer_sidebar() {
	dynamic_sidebar('sidebar-2');
}

?>