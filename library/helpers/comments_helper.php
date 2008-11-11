<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Outputs a text field and associated label.
 * 
 * Used in the comments reply form to reduce duplication and clean up the
 * template. Adds a wrapper div around the label and input field for ease of
 * styling.
 * 
 * @since 2.4
 * @uses required_field()
 * 
 * @param string $field
 * @param string $label
 * @param string $value
 * @param boolean $required
 * @param integer $size
 */
function comment_text_field($field, $label, $value = '', $required = false, $size = 20) { ?>
	<div class="text-wrap">
		<label for="<?php echo $field; ?>"><?php printf($label, required_field($required)); ?></label>
		<input class="<?php echo comment_field_classes(); ?>" type="text" name="<?php echo $field; ?>" id="<?php echo $field; ?>" value="<?php echo $value; ?>" size="<?php echo $size; ?>" />
	</div>
<?php }

/**
 * Builds the HTML classes for comment form text fields.
 * 
 * @since 2.4
 * 
 * @param string $classes
 * @param boolean $required
 * @return string
 */
function comment_field_classes($classes = '', $required = false) {
	$classes = trim($classes);
	if (strlen($classes) < 1) $classes = 'text';
	if ($required) $classes .= ' required';
	return apply_filters('comment_field_classes', $classes, $required);
}

/**
 * Returns a notice stating that a field is required.
 * 
 * Thrown into a function for reusability's sake, and to reduce the number of
 * sprintf()s and localisation strings cluttering up the comment form.
 * 
 * @since 2.4
 * 
 * @param boolean $required
 * @return string
 */
function required_field($required = true) {
	if ($required) return sprintf(
		'<span class="req-notice">(%s)</span>',
		__('required', 'tarski')
	);
}

?>