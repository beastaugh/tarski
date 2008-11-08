<label for="opt-misc-title">
	<input type="hidden" name="display_title" value="0" />
	<input type="checkbox" id="opt-misc-title" name="display_title" value="1" <?php if(get_tarski_option('display_title')) { echo 'checked="checked" '; } ?>/>
	<?php _e('Display site title','tarski'); ?>
</label>

<label for="opt-misc-tagline">
	<input type="hidden" name="display_tagline" value="0" />
	<input type="checkbox" id="opt-misc-tagline" name="display_tagline" value="1" <?php if(get_tarski_option('display_tagline')) { echo 'checked="checked" '; } ?>/>
	<?php _e('Display site tagline','tarski'); ?>
</label>

<?php if(!get_bloginfo('description')) { ?>
	<p><?php printf(
		__('Your tagline is currently %s and won&#8217;t be displayed.', 'tarski'),
		'<a href="'. admin_url('options-general.php') . '">'. __('blank','tarski'). '</a>'
	); ?></p>
<?php } ?>

<label for="opt-misc-cats">					
	<input type="hidden" name="show_categories" value="0" />
	<input type="checkbox" id="opt-misc-cats" name="show_categories" value="1" <?php if(get_tarski_option('show_categories')) { echo 'checked="checked" '; } ?>/>
	<?php _e('Show post categories','tarski'); ?>
</label>

<label for="opt-misc-tags">					
	<input type="hidden" name="tags_everywhere" value="0" />
	<input type="checkbox" id="opt-misc-tags" name="tags_everywhere" value="1" <?php if(get_tarski_option('tags_everywhere')) { echo 'checked="checked" '; } ?>/>
	<?php _e('Show tags everywhere','tarski'); ?>
</label>

<label for="opt-misc-pagination">
	<input type="hidden" name="use_pages" value="0" />
	<input type="checkbox" id="opt-misc-pagination" name="use_pages" value="1" <?php if(get_tarski_option('use_pages')) { echo 'checked="checked" '; } ?>/>
	<?php _e('Paginate index pages (such as the front page or monthly archives)','tarski'); ?>
</label>

<label for="opt-misc-centre">						
	<input type="hidden" name="centred_theme" value="0" />
	<input type="checkbox" id="opt-misc-centre" name="centred_theme" value="1" <?php if(get_tarski_option('centred_theme')) { echo 'checked="checked" '; } ?>/>
	<?php _e('Centre the theme','tarski'); ?>
</label>

<label for="opt-misc-janus">	
	<input type="hidden" name="swap_sides" value="0" />
	<input type="checkbox" id="opt-misc-janus" name="swap_sides" value="1" <?php if(get_tarski_option('swap_sides')) { echo 'checked="checked" '; } ?>/>
	<?php _e('Switch the column positions (left becomes right, and vice versa)','tarski'); ?>
</label>

<label for="opt-misc-titleswap">	
	<input type="hidden" name="swap_title_order" value="0" />
	<input type="checkbox" id="opt-misc-titleswap" name="swap_title_order" value="1" <?php if(get_tarski_option('swap_title_order')) { echo 'checked="checked" '; } ?>/>
	<?php _e('Reverse document title order (show site name last)','tarski'); ?>
</label>
