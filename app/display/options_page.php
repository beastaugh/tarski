<div id="tarski-options" class="wrap tarski-options<?php if(get_bloginfo("text_direction") == "rtl") { echo " rtl"; } ?>">
	
	<?php if(isset($_POST['submit']) && !get_tarski_option('deleted')) { ?>
	<div id="updated" class="updated fade">
		<?php if(isset($_POST['restore_options'])) { ?>
		<p><?php echo __('Tarski options have been restored.','tarski') . ' <a href="' . get_bloginfo('url') . '/">' . __('View site &raquo;','tarski') . '</a>'; ?></p>
		<?php } else { ?>
		<p><?php echo __('Tarski options have been updated.','tarski') . ' <a href="' . get_bloginfo('url') . '/">' . __('View site &raquo;','tarski') . '</a>'; ?></p>
		<?php } ?>
	</div>
	<?php } ?>
	
	<?php if(get_tarski_option('deleted')) { ?>
	<div class="updated fade">
		<form name="dofollow" action="<?php echo $tarski_options_link; ?>" method="post">
			<?php wp_nonce_field('update-options'); ?>
			<input type="hidden" name="restore_options" value="1" />
			<p><?php _e('You have deleted your Tarski options.','tarski'); ?> <input class="button" type="submit" name="submit" value="<?php _e('Restore Tarski Options &raquo;','tarski'); ?>" /></p>
		</form>
	</div>
	<?php } ?>
	
<form name="dofollow" action="<?php echo $tarski_options_link; ?>" method="post">
	
	<div id="tarski-options-header">
		<h2><?php _e('Tarski Options', 'tarski'); ?></h2>
		<p id="tarski-save-options">
			<input type="submit" class="button-secondary" name="submit" value="<?php _e('Save Options','tarski'); ?>" />
			<input type="hidden" name="page_options" value="'dofollow_timeout'" />
		</p>
		
		<p id="tarski-info">
			<a href="http://tarskitheme.com/help/"><?php _e('Tarski documentation','tarski'); ?></a>
			| <a href="http://tarskitheme.com/credits/"><?php _e('Credits &amp; Thanks','tarski'); ?></a>
			| <a href="http://tarskitheme.com/forum/"><?php _e('Forum','tarski'); ?></a>
		</p>
		
		<div class="clearer"></div>
	</div>
		
	<div id="tarski-update-notifier" class="secondary"><div class="section">
	<?php if ( (!detectWPMU() || detectWPMUadmin()) ) { ?>
	<?php if(can_get_remote()) { ?>
		<h3><?php _e('Update Notification','tarski'); ?></h3>
		<?php if(get_tarski_option('update_notification')) { ?>
			<p><?php _e('Tarski is set to notify you when an update is available.','tarski'); ?></p>
		<?php } else { ?>
			<p><?php _e('Tarski can be set to notify you when updates are available.','tarski'); ?></p>
		<?php } ?>
		<label for="update-on"><input type="radio" id="update-on" name="update_notification" value ="on" <?php if(get_tarski_option('update_notification')) { echo 'checked="checked" '; } ?>/> <?php _e('Update notification on (recommended)','tarski'); ?></label>
		<label for="update-off"><input type="radio" id="update-off" name="update_notification" value ="off" <?php if(!get_tarski_option('update_notification')) { echo 'checked="checked" '; } ?>/> <?php _e('Update notification off','tarski'); ?></label>
		<?php if(!cache_is_writable('version.atom') && get_tarski_option('update_notification')) { ?>
		<p class="tip"><?php printf( __('The version check could not be cached. To enable caching, follow the tutorial on the %s page.','tarski'), '<a href="http://tarskitheme.com/help/updates/notifier/">' . __('update notifier','tarski') . '</a>' ); ?></p>
		<?php } ?>
	<?php } else { ?>
		<h3><?php _e('Update Notification','tarski'); ?></h3>
		<p><?php printf(
			__('Your server appears to have %1$s disabled and %2$s not installed. This means that the update notifier will not work.','tarski'),
			'<a href="http://uk.php.net/manual/en/ref.filesystem.php"><code>allow_url_fopen</code></a>',
			'<a href="http://uk.php.net/manual/en/ref.curl.php"><code>libcurl</code></a>'
		); ?></p>
		<p><?php printf(
			__('You can read our %1$s on how to fix your server setup, but if you are unable to change it we recommend subscribing to either the %2$s or the %3$s in your feed reader, so that you can be alerted when new Tarski versions become available.','tarski'),
			'<a href="http://tarskitheme.com/help/updates/notifier/">' . __('documentation','tarski') . '</a>',
			'<a href="' . TARSKIVERSIONFILE . '">' . __('Tarski version feed','tarski') . '</a>',
			'<a href="http://tarskitheme.com/feed/">' . __('Tarski website feed','tarski') . '</a>'
		); ?></p>
	<?php } } ?>
	</div></div>
	
	<div class="primary"><div class="section">
		<h3><?php _e('Navigation Display','tarski'); ?></h3>
		<?php
		
		$pages = &get_pages('sort_column=post_parent,menu_order');
		
		if($pages) {
			echo '<p>'. __('Pages selected here will display in your navbar.','tarski'). "</p>\n";
			echo tarski_navbar_select($pages);
			echo '<input type="hidden" id="opt-collapsed-pages" name="collapsed_pages" value="' . get_tarski_option('collapsed_pages') . '" />' . "\n\n";			
			echo '<p class="tip">' . __('To change the order in which they appear, edit the &#8216;Page Order&#8217; value on each page.','tarski') . "</p>\n";
			
		} else {
			echo '<p>' . __('There are no pages to select navbar items from.','tarski') . "</p>\n";
		} ?>
		
		<h3><?php _e('Navigation Options','tarski'); ?></h3>
		
		<?php $categories = &get_categories('type=link&hide_empty=0'); ?>
		<label for="opt-nav-extlinkcat"><?php _e('Add external links to the navbar.','tarski'); ?></label>
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
		<p class="tip"><?php printf( __('You can add or edit links on the %s page. We recommend creating a link category specifically for the links you want displayed in your navbar, but you can use any category.','tarski'), '<a href="'. admin_url('link-manager.php') . '">'. __('Manage Links','tarski'). '</a>' ); ?></p>
		
		<label for="opt-nav-homename"><?php _e('Rename your &#8216;Home&#8217; link.','tarski'); ?></label>
		<input type="hidden" name="home_link_name" value="Home" />
		<input type="text" id="opt-nav-homename" name="home_link_name" value="<?php if(get_tarski_option('home_link_name')) { echo get_tarski_option('home_link_name'); } else { _e('Home','tarski'); } ?>" />
		 <p class="tip"><?php _e('This link is not displayed when you have a static front page.','tarski'); ?></p>
	</div></div>
	
	<div class="secondary">
		<div class="section">
			<h3><?php _e('Alternate Style','tarski'); ?></h3>
		<?php
		$style_dir = dir(TEMPLATEPATH . '/styles');
		if($style_dir) {
			while(($file = $style_dir->read()) !== false) {
				if(is_valid_tarski_style($file)) {
					$styles[] = $file;
				}
			}
		}
		if($style_dir && $styles) { ?>
			<select name="alternate_style" id="alternate_style" size="1">
				<option<?php if(!get_tarski_option('style')) { echo ' selected="selected"'; } ?> value=""><?php _e('Default style','tarski'); ?></option>
				<?php foreach($styles as $style) {
					if(get_tarski_option('style') == $style) {
						$status = ' selected="selected"';
					} else {
						$status = false;
					}
					printf(
						'<option%1$s value="%2$s">%3$s</option>'."\n",
						$status,
						$style,
						$style
					);
				} ?>
			</select>
		<?php } ?>
		
		<?php if(detectWPMU()) { // WPMU users ?>
			<p><?php _e('Tarski allows you to select an alternate style that modifies the default one. Choose from the list above.','tarski'); ?></p>
		<?php } else { // non-WPMU users ?>
			<p><?php printf( __('Tarski allows you to select an %1$s that modifies the default one. Choose from the list above, or upload your own to %2$s.','tarski'), '<a href="http://tarskitheme.com/help/styles/">'. __('alternate style','tarski'). '</a>', '<code>wp-content/themes/' . get_template() . '/styles/</code>' ); ?></p>
		<?php } ?>
		</div>
		
		<div class="section">
			<h3><?php _e('Asides Category','tarski'); ?></h3>
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
		</div>
		
		<div class="section">
			<h3><?php _e('Sidebar','tarski'); ?></h3>
			
			<p><?php _e('The sidebar for posts and pages can be the same as that for index pages, or use its own set of widgets.','tarski'); ?></p>
				
			<label for="sidebar-pp-type">
				<input type="hidden" name="sidebar_pp_type" value="0" />
				<input type="checkbox" id="sidebar-pp-type" name="sidebar_pp_type" value="main" <?php if(get_tarski_option('sidebar_pp_type') == 'main') { echo 'checked="checked" '; } ?>/>
				<?php _e('Same content as main sidebar?','tarski'); ?>
			</label>
		</div>
	</div>
	
	<div class="span"><div class="section">
		<h3><?php _e('Header Images', 'tarski'); ?></h3>
		
		<div id="tarski-headers">
			<?php
			$name = get_tarski_option('header');
			if ( $header_dir = dir(TEMPLATEPATH . '/headers' ) ) {
				while(($file = $header_dir->read()) !== false) {
					if(!preg_match('|^\.+$|', $file) && preg_match('@\-thumb.(jpg|png|gif)$@', $file)) {
						$header_images[] = $file;
					}
				}
				if ($header_dir || $header_images) {
					$count = 0;
					foreach($header_images as $header_image) {
						$count++;
						$header_name = str_replace('-thumb', '', $header_image); ?>
						<label for="header_<?php echo $header_name; ?>"><img class="header_image" alt="<?php echo $header_name; ?>" src="<?php echo get_bloginfo('template_directory') . '/headers/' . $header_image; ?>" /></label>
						<input id="header_<?php echo $header_name; ?>" name="header_image" class="crirHiddenJS" value="<?php echo $header_name; ?>" type="radio"<?php if(get_tarski_option('header') == $header_name) { echo ' checked="checked"'; } ?> />
					<?php }
				}
			} ?>
			</div>
			
			<p><?php printf( __('Choose a header image by clicking on it. The current image is the %s one.','tarski'), '<span class="highlight">' . __('highlighted','tarski') . '</span>' ); ?></p>
		<?php if(!detectWPMU()) { ?>
		<div class="tip">
			<p><?php printf( __('You can upload your own header images (.gif, .jpg or .png) to %s.','tarski'), '<code>wp-content/themes/' . get_template() . '/headers/</code>' ); ?></p>
			<p><?php printf( __('Make sure that you upload a thumbnail file as well. If your image is named %1$s, the corresponding thumbnail file should be named %2$s.','tarski'), '<code>'. __('example','tarski'). '.jpg</code>', '<code>'. __('example','tarski'). '-thumb.jpg</code>'); ?></p>
		</div>
		<?php } ?>
	</div></div>
	
	<div class="primary"><div id="tarski-miscellaneous-options" class="section">
		<h3><?php _e('Miscellaneous Options','tarski'); ?></h3>
		
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
			<p class="tip"><?php echo __('Your tagline is currently ','tarski'). '<a href="'. admin_url('options-general.php') . '">'. __('blank','tarski'). '</a>'. __(' and won&#8217;t be displayed.')  ?></p>
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
	</div></div>
	
</form>
	
	<?php if(get_option('tarski_options') && !get_tarski_option('deleted')) { ?>
	<div class="secondary">
		<div class="section">
			<h3><?php _e('Reset Options', 'tarski'); ?></h3>
			<form action="<?php echo $tarski_options_link; ?>" method="post">
				<p><input class="button-secondary" type="submit" name="submit" value="<?php _e('Reset Tarski&#8217;s options to their default values','tarski'); ?>" /></p>
				<?php wp_nonce_field('update-options'); ?>
				<input type="hidden" name="delete_options" value="1" />
			</form>
			<p class="tip"><?php _e('If you change your mind, you&#8217;ll have three hours to restore your options before they&#8217;re removed for good.','tarski'); ?></p>
		</div>
	</div>
	<?php } ?>
	
	
	<div class="clearer"></div>
</div>
