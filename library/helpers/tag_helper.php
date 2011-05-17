<?php

/**
 * Implodes an array and adds a final conjuction.
 *
 * Given the array <code>array('John', 'Paul', 'George', 'Ringo')</code> it will
 * return the string <code>'John, Paul, George and Ringo'</code>.
 *
 * @since 2.0
 *
 * @param $array array
 * @param $glue string
 * @param $last_connective string
 * @return string
 */
function implode_proper($array, $glue = NULL, $last_connective = NULL) {
    if (!is_array($array) || empty($array)) return '';
    
    if ($glue == NULL)
        $glue = __(', ', 'tarski');
    
    if ($last_connective == NULL)
        $last_connective = __('and', 'tarski');
    
    $last_value = array_pop($array);
    
    $output = !empty($array)
        ? implode($glue, $array) . " $last_connective $last_value"
        : $last_value;
    
    return $output;
}

/**
 * Outputs all tags for a tag archive
 *
 * Tag intersections and unions currently don't have a simple, single template
 * function. This provides one.
 *
 * @example multiple_tag_titles('<em>%s</em>') will wrap every printed tag in
 * an HTML emphasis element.
 *
 * @since 2.0
 *
 * @global $wpdb object
 * @param $format string
 * @return string
 *
 * @hook filter multiple_tag_titles
 * Filter the value returned when generating the title of multiple (union or
 * intersection) tag archive page.
 */
if (!function_exists('multiple_tag_titles')) {
    function multiple_tag_titles($format = '') {
        global $wpdb;
        
        if (!is_tag()) return;
        
        if ($tag_slugs = get_query_var('tag_slug__and'))
            $connective = __('and', 'tarski');
        elseif ($tag_slugs = get_query_var('tag_slug__in'))
            $connective = __('or', 'tarski');
        else
            $single_tag = intval(get_query_var('tag_id'));
        
        $tags = array();
        
        if ($tag_slugs) {
            foreach ($tag_slugs as $tag_slug) {
                $tag = get_term_by('slug', $tag_slug, 'post_tag', OBJECT, 'display');
                if (!is_wp_error($tag) && !empty($tag->name))
                    $tags[] = $tag->name;
            }
        } elseif ($single_tag) {
            $tag = &get_term($single_tag, 'post_tag', OBJECT, 'display');
            
            if (is_wp_error($tag) || empty($tag->name))
                return false;
            else
                $tags[] = $tag->name;
        } else {
            return;
        }
        
        if (strlen($format) > 0) {
            foreach ($tags as $index => $tag)
                $tags[$index] = sprintf($format, $tag);
        }
        
        $tags = implode_proper($tags, __(', ', 'tarski'), $connective);
        $tags = apply_filters('multiple_tag_titles', $tags);
        
        return $tags;
    }
}

/**
 * Append tags to posts.
 *
 * @since 2.0
 *
 * @return void
 */
function add_post_tags() {
    if (is_404()) return;
    
    $aside = has_post_format('aside') ||
        in_category(get_tarski_option('asidescategory'));
    
    if (is_singular() || (get_tarski_option('tags_everywhere') && !$aside)) {
        $tag_html = '<p class="tagdata"><strong>' .
            __('Tags', 'tarski') . ':</strong> ';
        the_tags($tag_html, ', ', '</p>'."\n");
    }
}

?>