<div class="secondary">

<?php th_sidebar(); ?>

<?php if(!get_tarski_option('sidebar_onlyhome') || !(is_single() || is_page())) { ?>

	<?php if(get_tarski_option('sidebar_type') == 'widgets') { // Widgets sidebar ?>

		<div class="widgets">
			<?php dynamic_sidebar(__('Main Sidebar','tarski')); ?>
		</div>
	
	<?php } elseif(get_tarski_option('sidebar_type') == 'tarski') { // Tarski sidebar ?>

		<?php if(get_tarski_option('sidebar_custom')) { // Blurb ?>
			<div class="content">
				<?php tarski_sidebar_custom(); ?>
			</div>
		<?php } if(get_tarski_option('sidebar_pages')) { // Pages ?>
			<h3><?php _e('Pages','tarski'); ?></h3>
			<ul class="navlist xoxo">
				<?php wp_list_pages('sort_column=post_title&title_li='); ?>
			</ul>
		<?php } if(get_tarski_option('sidebar_links')) { // Links ?>
			<div class="bookmarks xoxo">
				<?php wp_list_bookmarks(tarski_sidebar_links()); ?>
			</div>
		<?php } ?>
	
	<?php } elseif(get_tarski_option('sidebar_type') == 'custom') { // Custom sidebar ?>

		<?php if(file_exists(TEMPLATEPATH . '/user-sidebar.php')) { ?>
			<?php include(TEMPLATEPATH . '/user-sidebar.php'); ?>
		<?php } else { ?>
			<h3><?php _e('Error', 'tarski'); ?></h3>
			<p><?php sprintf( __('%s not found.', 'tarski'), '<code>user-sidebar.php</code>' ); ?></p>
		<?php } ?>

	<?php } ?>

	<?php wp_meta(); ?>
	<?php @include(TEMPLATEPATH."/constants.php"); ?>
	<?php tarski_output_constant($sidebarBottomInclude); ?>
	
<?php } ?>

</div>
<?php // Reset post data
$wp_the_query->current_post--;
setup_postdata($wp_query->next_post());
?>