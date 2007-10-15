<?php global $wpdb; // Need this for all the navbar jazz ?>

<?php if(isset($_POST['Submit']) && !get_tarski_option('deleted')) { ?>
	<div id="updated" class="updated fade">
		<?php if(isset($_POST['restore_options'])) { ?>
			<p><?php echo __('Tarski Options have been restored.','tarski'). ' <a href="'. get_bloginfo('url'). '/">'. __('View site &raquo;','tarski'). '</a>'; ?></p>
		<?php } else { ?>
			<p><?php echo __('Tarski Options have been updated.','tarski'). ' <a href="'. get_bloginfo('url'). '/">'. __('View site &raquo;','tarski'). '</a>'; ?></p>
		<?php } ?>
	</div>
<?php } ?>


<?php if(get_tarski_option('deleted')) { ?>
	<div class="updated fade">
		<form name="dofollow" action="" method="post">
			<?php wp_nonce_field('update-options') ?>
			<input type="hidden" name="restore_options" value="1" />
			<p><?php _e('You have deleted your Tarski options.','tarski'); ?> <input class="options-tidy-submit" type="submit" name="Submit" value="<?php _e('Restore Tarski Options &raquo;','tarski'); ?>"></p>
		</form>
	</div>
<?php } ?>


<?php if(!detectWPMU() || detectWPMUadmin()) { ?>
	<?php tarski_update_notifier("options_page"); ?>
<?php } ?>

<?php if(get_tarski_option('debug')) { global $tarski_options; ?>
	<div class="updated">
		<pre>
			<?php print_r($tarski_options); ?>
		</pre>
	</div>
<?php } ?>


<div class="wrap<?php if(get_bloginfo("text_direction") == "rtl") { echo " rtl"; } ?>">
	
	<h2><?php _e('Tarski Options','tarski'); ?></h2>

	<form name="dofollow" action="" method="post">
		
		<?php wp_nonce_field('update-options') ?>
		
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Options &raquo;','tarski'); ?>" /></p>
		<input type="hidden" name="page_options" value="'dofollow_timeout'" />


		<div class="section">

		<?php if(!detectWPMU() || detectWPMUadmin()) { ?>
			<fieldset class="primary radiolist">
				<h3><?php _e('Update Notification','tarski'); ?></h3>
				<?php if(get_tarski_option('update_notification')) { ?>
					<p><?php _e('Tarski is set to notify you when an update is available.','tarski'); ?></p>
				<?php } else { ?>
					<p><?php _e('Tarski can be set to notify you when updates are available.','tarski'); ?></p>
				<?php } ?>
				<label for="update-on"><input type="radio" id="update-on" name="update_notification" value ="on" <?php if(get_tarski_option('update_notification')) { echo 'checked="checked" '; } ?>/> <?php _e('Update notification on (recommended)','tarski'); ?></label>
				<label for="update-off"><input type="radio" id="update-off" name="update_notification" value ="off" <?php if(!get_tarski_option('update_notification')) { echo 'checked="checked" '; } ?>/> <?php _e('Update notification off','tarski'); ?></label>
				<?php if(!cache_is_writable() && get_tarski_option('update_notification')) { ?>
				<p class="insert"><?php echo sprintf( __('The version check could not be cached. To enable caching, follow the tutorial on the %s page.','tarski'), '<a href="http://tarskitheme.com/help/updates/notifier/">'. __('update notifier','tarski'). '</a>' ); ?></p>
				<?php } ?>
				<p class="insert"><strong><?php _e('Privacy notice: ','tarski'); ?></strong><?php _e('The update notifier does not transmit any information about you or your website.'); ?></p>
			</fieldset>
		<?php } ?>

			<fieldset class="secondary">
				<h3><?php _e('Footer Options','tarski'); ?></h3>
				<textarea name="about_text" rows="5" cols="30" id="footer_blurb"><?php echo stripslashes(get_tarski_option('blurb')); ?></textarea>
				<label for="footer_blurb" class=""><?php _e('Write something about yourself here, and it will appear in the footer. Deleting the content disables it.','tarski'); ?></label>
				
				<label for="opt-footer-recent" class="spaced-out">
					<input type="hidden" name="footer[recent]" value="0" />
					<input type="checkbox" name="footer[recent]" value="1"  id="opt-footer-recent" <?php if(get_tarski_option('footer_recent')) { echo 'checked="checked" '; } ?>/>
					<?php _e('Show recent articles in the footer','tarski'); ?>
				</label>
			</fieldset>
			<hr />
		</div>



		<div class="section">
			<fieldset class="primary radiolist">
				<h3><?php _e('Pick a Sidebar&hellip;','tarski'); ?></h3>
				<p><?php _e('Choose either Tarski&#8217;s built-in sidebar options, those afforded by WordPress Widgets, or write your own sidebar code.','tarski'); ?></p>
				
				<label for="option-ts"><input type="radio" id="option-ts" name="sidebartype" value="tarski"<?php if(get_tarski_option('sidebar_type') == 'tarski') { echo " checked=\"checked\""; } ?> /> <?php _e('Tarski sidebar options','tarski'); ?></label>
				<?php if(function_exists('register_sidebar')) { ?>
					<label for="option-ws"><input type="radio" id="option-ws" name="sidebartype" value="widgets"<?php if(get_tarski_option('sidebar_type') == 'widgets') { echo " checked=\"checked\""; } ?> /> <?php _e('WordPress Widgets','tarski'); ?></label>
				<?php } else { ?>
					<label for="option-ws"><input type="radio" id="option-ws" name="sidebartype" value="" disabled="disabled" /> <?php _e('Install (or activate) <a href="http://automattic.com/code/widgets/">WordPress Widgets</a>.','tarski'); ?></label>
				<?php } ?>
				<?php if(!detectWPMU()) { // custom sidebar only available in non-WPMU stuff ?>
				<label for="option-fs"><input type="radio" id="option-fs" name="sidebartype" value="custom"<?php if(get_tarski_option('sidebar_type') == 'custom') { echo " checked=\"checked\""; } ?> /> <?php _e('Alternate sidebar file','tarski'); ?></label>
				<?php } // end non-WPMU-only block ?>
				
				<label class="spaced-out" for="opt-sidebar-onlyhome">
					<input type="hidden" name="sidebar[onlyhome]" value="0" />
					<input type="checkbox" name="sidebar[onlyhome]" value="1"  id="opt-sidebar-onlyhome" <?php if(get_tarski_option('sidebar_onlyhome')) { echo 'checked="checked" '; } ?>/>
					<?php _e('Only display the sidebar on index pages','tarski'); ?>
				</label>
				<p><?php _e('The home, archive and search pages are index pages; this option is good for people with long sidebars.','tarski')?></p>
			</fieldset>

			<fieldset class="secondary">
			<h3><?php _e('&hellip;and configure it.','tarski'); ?></h3>
			
				<div id="tarski-sidebar-section" class="insert"<?php if(get_tarski_option('sidebar_type') != 'tarski') { echo ' style="display: none;"'; } ?>>
					<label for="opt-sidebar-pages">
						<input type="hidden" name="sidebar[pages]" value="0" />
						<input type="checkbox" name="sidebar[pages]" value="1" id="opt-sidebar-pages" <?php if(get_tarski_option('sidebar_pages')) { echo 'checked="checked" '; } ?>/>
						<?php _e('Pages list','tarski'); ?>
					</label>
	
					<label for="opt-sidebar-links">
						<input type="hidden" name="sidebar[links]" value="0" />
						<input type="checkbox" name="sidebar[links]" value="1" id="opt-sidebar-links" <?php if(get_tarski_option('sidebar_links')) { echo 'checked="checked" '; } ?>/>
						<?php _e('Links list','tarski'); ?>
					</label>

					<p><?php _e('Anything you put into this custom content area, like text, images etc. will show up in the sidebar below the options above. Leaving the field blank disables it.','tarski'); ?></p>
					
					<textarea name="sidebar[custom]" rows="5" cols="30" id="sidebar_custom"><?php echo stripslashes(htmlspecialchars(get_tarski_option('sidebar_custom'))); ?></textarea>
				</div>
			
				
				<?php if(file_exists(ABSPATH . '/wp-includes/widgets.php')) { $widgetsLink = '/wp-admin/widgets.php'; } else { $widgetsLink = '/wp-admin/themes.php?page=widgets/widgets.php'; } ?>
				<div id="widgets-sidebar-section" class="insert"<?php if(get_tarski_option('sidebar_type') != 'widgets') { echo ' style="display: none;"'; } ?>>
					<p><?php echo sprintf( __('To configure your Sidebar Widgets, go to the %s page and select the widgets you&#8217;d like to use.','tarski'), '<a href="' . get_bloginfo('wpurl') . $widgetsLink . '">' . __('Widgets configuration','tarski') . '</a>' ); ?></p>
				</div>
				
				<?php if(!detectWPMU()) { ?>
				<div id="custom-sidebar-section" class="insert"<?php if(get_tarski_option('sidebar_type') != 'custom') { echo ' style="display: none;"'; } ?>>
					<p><?php echo sprintf( __('To use your own custom sidebar code, upload a file named %s to your Tarski directory.','tarski'), "<code>user-sidebar.php</code>" ); ?></p>
				</div>
				<?php } ?>
				
				
			</fieldset>
			<hr />
		</div>



		<div class="section">
			<fieldset class="primary">			
				<h3><?php _e('Alternate Style','tarski'); ?></h3>
					<?php
					$style_dir = @ dir(TEMPLATEPATH . '/styles');
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
						<p><?php echo __('Tarski allows you to select an %s that modifies the default one. Choose from the list above.','tarski'); ?></p>
					<?php } else { // non-WPMU users ?>
						<p><?php printf( __('Tarski allows you to select an %1$s that modifies the default one. Choose from the list above, or upload your own to %2$s.','tarski'), '<a href="http://tarskitheme.com/help/styles/">'. __('alternate style','tarski'). '</a>', '<code>wp-content/themes/' . get_template() . '/styles/</code>' ); ?></p>
					<?php } ?>

				<h3><?php _e('Header Image','tarski'); ?></h3>
				<?php if(function_exists('add_custom_image_header')) { ?>
					<p><?php printf( __('You may wish to use one of these stock headers, or upload your own via the %s tab.', 'tarski'), '<a href="themes.php?page=custom-header">' . __('Custom Image Header', 'tarski') . '</a>' ); ?></p>
				<?php } ?>
				<?php if(get_theme_mod('header_image')) { ?>
					<p class="insert"><strong><?php printf( __('You are currently using a custom header uploaded via WordPress. To use a stock icon instead, go to the %s tab and click &#8216;Restore Original Header&#8217;.', 'tarski'), '<a href="themes.php?page=custom-header">' . __('Custom Image Header', 'tarski') . '</a>' ); ?></strong></p>
				<?php } ?>
					
					<div id="tarski-headers">
						<?php
						$name = get_tarski_option('header');

						$header_dir = @ dir(TEMPLATEPATH . '/headers');	

						if ($header_dir) {
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
					
						<p><?php printf( __('Choose a header image by clicking on it. The current image is the %s.','tarski'), '<span class="highlight">' . __('highlighted one','tarski') . '</span>' ); ?></p>
					<?php if(!detectWPMU()) { ?>
					<div class="insert">
						<p><?php printf( __('You can upload your own header images (.gif, .jpg or .png) to %s.','tarski'), '<code>wp-content/themes/' . get_template() . '/headers/</code>' ); ?>
						<p><?php printf( __('Make sure that you upload a thumbnail file as well. If your image is named %1$s, the corresponding thumbnail file should be named %2$s.','tarski'), '<code>'. __('example','tarski'). '.jpg</code>', '<code>'. __('example','tarski'). '-thumb.jpg</code>'); ?></p>
					</div>
					<?php } ?>
			</fieldset>

				
			<fieldset class="secondary">

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

				<h3><?php _e('Navigation Display','tarski'); ?></h3>
				<?php
				global $wpdb;

				$results = $wpdb->get_results("SELECT ID, post_title from $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_parent, menu_order");
					
				$nav_pages = explode(',', get_tarski_option('nav_pages'));
					
				if($results) {
					echo '<p>'. __('Pages selected here will display in your navbar.','tarski'). "</p>\n";
					foreach($results as $page) {
						echo '<label for="opt-pages-'. $page->ID. '"><input type="checkbox" id="opt-pages-'. $page->ID. '" name="nav_pages[]" value="'. $page->ID. '"';
						if(in_array($page->ID, $nav_pages)) { echo ' checked="checked"'; }
						echo " />\n";
						echo $page->post_title. '&nbsp;<a title="'. __('View this page','tarski'). '" href="'. get_permalink($page->ID). '">&#8599;</a></label>'."\n";
					}
					echo '<p>' . __('To change the order in which they appear, edit the &#8216;Page Order&#8217; value on each page.','tarski') . "</p>\n";
				} else {
					echo '<p>' . __('There are no pages to select navbar items from.','tarski') . "</p>\n";
				} ?>
				
				<h3><?php _e('Navigation Options','tarski'); ?></h3>
				
				<?php $categories = &get_categories('type=link&hide_empty=0'); ?>
				<label for"opt-nav-extlinkcat"><?php _e('Add external links to the navbar.','tarski'); ?></label>
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
				<p class="insert"><?php printf( __('You can add or edit links on the %s page. We recommend creating a link category specifically for the links you want displayed in your navbar, but you can use any category.','tarski'), '<a href="'. get_bloginfo('wpurl'). '/wp-admin/link-manager.php">'. __('Blogroll','tarski'). '</a>' ); ?></p>
				
				<label for="opt-nav-homename"><?php _e('Rename your &#8216;Home&#8217; link.','tarski'); ?></label>
				<input type="hidden" name="home_link_name" value="Home" />
				<input type="text" id="opt-nav-homename" name="home_link_name" value="<?php if(get_tarski_option('home_link_name')) { echo get_tarski_option('home_link_name'); } else { _e('Home','tarski'); } ?>" />
				 <p><?php _e('Note that this link is not displayed when you have a static front page.','tarski'); ?></p>
				
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
					<p class="insert"><?php echo __('Your tagline is currently ','tarski'). '<a href="'. get_bloginfo('wpurl'). '/wp-admin/options-general.php">'. __('blank','tarski'). '</a>'. __(' and won&#8217;t be displayed.')  ?></p>
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
					<input type="hidden" name="centered_theme" value="0" />
					<input type="checkbox" id="opt-misc-centre" name="centered_theme" value="1" <?php if(get_tarski_option('centered_theme')) { echo 'checked="checked" '; } ?>/>
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
				
				
				<h3><?php _e('Feed Options','tarski'); ?></h3>
				<p><?php printf(__('Tarski can be set to link to either %s or Atom feeds.','tarski'), '<acronym title="' . __('Really Simple Syndication','tarski') . '">RSS</acronym>'); ?></p>
					
				<label for="feed-atom"><input type="radio" id="feed-atom" name="feed_type" value ="atom" <?php if(get_tarski_option("feed_type") == "atom") { echo 'checked="checked" '; } ?>/> <?php _e('Atom','tarski'); ?></label>
				<label for="feed-rss2"><input type="radio" id="feed-rss2" name="feed_type" value ="rss2" <?php if(get_tarski_option("feed_type") != "atom") { echo 'checked="checked" '; } ?>/> <?php printf('<acronym title="%s">RSS</acronym>', __('Really Simple Syndication','tarski')); ?></label>
				

			</fieldset>
			<hr />
		</div>
		
	<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Options &raquo;','tarski'); ?>" /></p>

</form>
</div>

<?php if(get_option('tarski_options') && !get_tarski_option('deleted')) { ?>
	<div class="wrap">
		<form name="dofollow" action="" method="post">
			<?php wp_nonce_field('update-options') ?>
			<input type="hidden" name="delete_options" value="1" />
			<p><?php _e('Fed up with your Tarski Options? Want to reset to the defaults? Hit this button!','tarski'); ?> <input type="submit" name="Submit" value="<?php _e('Delete Tarski Options &raquo;','tarski'); ?>"></p>
		</form>
	</div>
<?php } ?>

<div class="wrap">
	<p class="info"><?php printf( __('The %1$s is full of useful stuff &middot; %2$s'), '<a href="http://tarskitheme.com/help/">' . __('Tarski documentation','tarski') . '</a>', '<a href="http://tarskitheme.com/credits/">' . __('Credits &amp; Thanks','tarski') . '</a>'); ?></p>
</div>