<?php $categories = &get_categories('type=link&hide_empty=0'); ?>
<div class="option borderless">
	<label for="opt-nav-extlinkcat"><?php _e('Add external links to the navbar','tarski'); ?></label>
	<select name="nav_extlinkcat" id="opt-nav-extlinkcat" size="1">
		<option value="0"><?php _e('No external links','tarski'); ?></option>
		<?php foreach($categories as $link_cat) { ?>
			<?php if(get_tarski_option('nav_extlinkcat') == $link_cat->cat_ID) {
				$status = ' selected="selected"';
			} else {
				$status = false;
			}
			printf(
				'<option'. '%1$s'. ' value="%2$s">%3$s</option>',
				$status,
				$link_cat->cat_ID,
				$link_cat->cat_name
			); ?>
		<?php } ?>
	</select>
	<p><?php printf( __('You can add or edit links on the %s page. We recommend creating a link category specifically for the links you want displayed in your navbar, but you can use any category.','tarski'), '<a href="'. admin_url('link-manager.php') . '">'. __('Manage Links','tarski'). '</a>' ); ?></p>
</div>

<div class="option">
	<label for="opt-nav-homename"><?php _e('Rename your &#8216;Home&#8217; link','tarski'); ?></label>
	<input type="hidden" name="home_link_name" value="Home" />
	<input class="text" class="text" type="text" id="opt-nav-homename" name="home_link_name" value="<?php if(get_tarski_option('home_link_name')) { echo get_tarski_option('home_link_name'); } else { _e('Home','tarski'); } ?>" />
	<p><?php _e('This link is not displayed when you have a static front page.','tarski'); ?></p>
</div>
