<select name="asides_category" id="asides_category">
	<option <?php if(!get_tarski_option('asidescategory')) { echo 'selected="selected" '; } ?>value="0"><?php _e('Disable asides','tarski'); ?></option>
	<?php $asides_cats = &get_categories('hide_empty=0');
	if($asides_cats) {
		foreach ($asides_cats as $cat) {
			if(($cat->cat_ID) == get_tarski_option('asidescategory')) {
				$status = 'selected ="selected" ';
			} else {
				$status = false;
			}
			echo '<option '. $status. 'value="'. $cat->cat_ID. '">'. $cat->cat_name. '</option>';
		}
	} ?>
</select>
<p><?php echo __('This option will make Tarski display posts from the selected category in the ','tarski') . '<a href="http://photomatt.net/2004/05/19/asides/">' . __('Asides','tarski') . '</a>' . __(' format. Asides are short posts, usually only a single paragraph, and Tarski displays them in a condensed format without titles.','tarski'); ?></p>
