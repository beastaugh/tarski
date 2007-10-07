<div class="secondary">
<?php @include(TEMPLATEPATH."/constants.php"); ?>
<?php th_sidebar(); ?>
<?php if(is_search()) { ?>
	<?php include(TEMPLATEPATH . '/searchform.php'); ?>
	<?php tarski_output_constant($sidebarBottomInclude); ?>
<?php } else { ?>

	<?php if(!get_tarski_option('sidebar_onlyhome') || !(is_single() || is_page())) { ?>
	
		<?php if(get_tarski_option('sidebar_type') == 'widgets') { ?>
	
			<div class="widgets">
				<?php dynamic_sidebar(__('Main Sidebar','tarski')); ?>
			</div>
	
		<?php } elseif(get_tarski_option('sidebar_type') == 'tarski') { ?>
	
			<?php if(get_tarski_option('sidebar_custom')) { ?>
				<div class="content">
					<?php echo get_tarski_sidebar_custom(); ?>
				</div>
			<?php } ?>
			<?php if(get_tarski_option('sidebar_pages')) { // pages block ?>
				<h3><?php _e('Pages','tarski'); ?></h3>
				<ul class="navlist xoxo">
					<?php wp_list_pages('sort_column=post_title&title_li='); ?>
				</ul>
			<?php } // end pages block ?>
			<?php if(get_tarski_option('sidebar_links')) { // links block ?>
				<div class="bookmarks xoxo">
				<?php wp_list_bookmarks('category_before=&category_after=&title_before=<h3>&title_after=</h3>&show_images=0&show_description=0'); ?>
				</div>
			<?php } // end links block ?>
	
		<?php } elseif(get_tarski_option('sidebar_type') == 'custom') { ?>

			<?php if(file_exists(TEMPLATEPATH . '/user-sidebar.php')) {
				include(TEMPLATEPATH . '/user-sidebar.php');
			} else {
				echo '<h3>' . __('Error', 'tarski') . '</h3><p><code>user-sidebar.php</code> ' . __('not found.', 'tarski') . '</p>';
			} ?>

		<?php } ?>

		<?php wp_meta(); ?>
		<?php tarski_output_constant($sidebarBottomInclude); ?>
	<?php } // end onlyhome if ?>
<?php } // end search else ?>
<?php // Reset the post data incase part of the sidebar code messed it up
$wp_the_query->current_post--;
setup_postdata($wp_query->next_post()); ?>
</div>
