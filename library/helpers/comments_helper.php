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
		<input type="text" name="<?php echo $field; ?>" id="<?php echo $field; ?>" value="<?php echo $value; ?>" size="<?php echo $size; ?>" />
	</div>
<?php }

/**
 * Returns a notice stating that a field is required.
 * 
 * Thrown into a function for reusability's sake, and to reduce the number of
 * sprintf()s and localisation strings cluttering up the comment form.
 * 
 * @since 2.4
 * @param boolean $req
 * @return string
 */
function required_field($required = true) {
	if ($required) return sprintf(
		'<span class="req-notice">(%s)</span>',
		__('required', 'tarski_404_content')
	);
}

?>