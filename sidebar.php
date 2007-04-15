<?php if (is_search()) { ?>
<div class="secondary">
	<?php include (TEMPLATEPATH . "/searchform.php"); ?>
</div>
<?php } elseif(!get_tarski_option('sidebar_onlyhome') || !(is_single() || is_page())) { ?>

<div class="secondary">
<?php @include(TEMPLATEPATH . '/constants.php'); echo $sidebarTopInclude; ?>

<?php if(get_tarski_option('sidebar_type') == 'widgets') { ?>
	<div class="widgets">
		<?php dynamic_sidebar(__('Main Sidebar', 'tarski')); ?>
	</div>
<?php } elseif(get_tarski_option('sidebar_type') == 'tarski') { ?>
	<?php if($sidebar_custom = get_tarski_option('sidebar_custom')) {
		echo '<div class="content">' .  wpautop(wptexturize(stripslashes($sidebar_custom))) . '</div>';
	} ?>
	
	<?php if(get_tarski_option('sidebar_pages')) { // pages block ?>
		<h3><?php _e('Pages', 'tarski'); ?></h3>
		<ul class="navlist">
			<?php wp_list_pages('sort_column=post_title&title_li='); ?>
		</ul>
	<?php } // end pages block ?>

	<?php if(get_tarski_option('sidebar_comments') && function_exists('blc_latest_comments')) { // comments block ?>
		<h3><?php _e('Comments', 'tarski'); ?></h3>
		<ul class="navlist">
			<?php blc_latest_comments(5, 6, false, "<li>", "</li>"); ?>
		</ul>
	<?php } ?>
	
	<?php if(get_tarski_option('sidebar_links')) { // links block ?>
		<div class="bookmarks">
		<?php wp_list_bookmarks('category_before=&category_after=&title_before=<h3>&title_after=</h3>&show_images=0&show_description=0'); ?>
		</div>
	<?php } // end links block ?>
<?php } elseif(get_tarski_option('sidebar_type') == 'custom') { ?>
	<?php if(file_exists(TEMPLATEPATH . '/user-sidebar.php')) {
		@include(TEMPLATEPATH . '/user-sidebar.php');
	} else {
		echo '<h3>' . __('Error', 'tarski') . '</h3><p><code>user-sidebar.php</code> ' . __('not found.', 'tarski') . '</p>';
	} ?>
<?php } ?>

<?php echo $sidebarBottomInclude; ?>
<?php wp_meta(); ?>

</div>

<?php } else { // end onlyhome if 
	echo '<div class="secondary">';
	@include(TEMPLATEPATH . '/constants.php');
	echo $noSidebarInclude;
	echo '</div>';
} ?>