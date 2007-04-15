<?php
/*
Template Name: Links
*/
?>
<?php @include(TEMPLATEPATH . '/constants.php'); ?>
<?php get_header(); ?>
<div class="primary archive">
	<div class="meta">
		<h1><?php _e('Links', 'tarski'); ?></h1>
	</div>
	<div class="bookmarks">
	<?php wp_list_bookmarks('category_before=&category_after=&title_before=<h3>&title_after=</h3>&show_images=0&show_description=0'); ?>
	<?php echo $pageEndInclude; ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>