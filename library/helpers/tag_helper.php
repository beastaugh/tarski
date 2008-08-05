<?php

/**
 * wrap_values_in_element() - Wraps array values in the specified HTML element
 * 
 * Given the array <code>array('Bread', 'Milk', 'Cheese')</code>, if the specified
 * HTML element were <code>'li'</code> it would return the array
 * <code>array('<li>Bread</li>', '<li>Milk</li>', '<li>Cheese</li>')</code>.
 * @since 2.0
 * @param $array array
 * @param $element string
 * @return array
 */
function wrap_values_in_element($array, $element) {
	if(!check_input($array, 'array') || empty($array))
		return;
	
	foreach($array as $value)
		$output[] = "<$element>$value</$element>";
	
	return $output;
}

/**
 * implode_proper() - Implodes an array and adds a final conjuction.
 * 
 * Given the array <code>array('John', 'Paul', 'George', 'Ringo')</code> it will
 * return the string <code>'John, Paul, George and Ringo'</code>.
 * @since 2.0
 * @param $array array
 * @param $glue string
 * @param $last_connective string
 * @return string
 */
function implode_proper($array, $glue = NULL, $last_connective = NULL) {
	if ( !check_input($array, 'array') || count($array) == 0 )
		return;
	
	if ($glue == NULL)
		$glue = __(', ', 'tarski');
	
	if ($last_connective == NULL)
		$last_connective = __('and', 'tarski');
	
	$last_value = array_pop($array);
	
	if ( count($array) )
		$output = implode($glue, $array) . " $last_connective $last_value";
	else
		$output = $last_value;
	
	return $output;
}

/**
 * multiple_tag_titles() - Outputs all tags for a tag archive
 * 
 * Tag intersections and unions currently don't have a simple, single template
 * function. This provides one.
 * 
 * @example multiple_tag_titles('<em>%s</em>') will wrap every printed tag in
 * an HTML emphasis element.
 * @since 2.0
 * @global $wpdb object
 * @param $format string
 * @return string
 * @hook filter multiple_tag_titles
 * Filter the value returned when generating the title of multiple (union or
 * intersection) tag archive page.
 */
if ( !function_exists('multiple_tag_titles') ) {
function multiple_tag_titles($format = '') {
	global $wpdb;
	
	if ( !is_tag() )
		return;
	
	if ( $tag_slugs = get_query_var('tag_slug__and') )
		$connective = __('and');
	elseif ( $tag_slugs = get_query_var('tag_slug__in') )
		$connective = __('or');
	else
		$single_tag = intval( get_query_var('tag_id') );
	
	$tags = array();
	if ( $tag_slugs ) {
		foreach ( $tag_slugs as $tag_slug ) {
			$tag = get_term_by('slug', $tag_slug, 'post_tag', OBJECT, 'display');
			if ( !is_wp_error($tag) && !empty($tag->name) )
				$tags[] = $tag->name;
		}
	} elseif ( $single_tag ) {
		$tag = &get_term($single_tag, 'post_tag', OBJECT, 'display');
		if ( is_wp_error($tag) || empty($tag->name) )
			return false;
		else
			$tags[] = $tag->name;
	} else {
		return;
	}
	
	if ( strlen($format) > 0 ) {
		foreach ( $tags as $index => $tag )
			$tags[$index] = sprintf($format, $tag);
	}
			
	$tags = implode_proper($tags, __(', ', 'tarski'), $connective);
	$tags = apply_filters('multiple_tag_titles', $tags);
	return $tags;
}
}

/**
 * add_post_tags() - Appends tags to posts.
 * 
 * @since 2.0
 * @return string
 */
function add_post_tags() {
	if (is_single() || (get_tarski_option('tags_everywhere') && !in_category(get_tarski_option('asidescategory')))) {
		the_tags('<p class="tagdata"><strong>'. __('Tags','tarski'). ':</strong> ', ', ', '</p>'."\n");
	}
}

?>