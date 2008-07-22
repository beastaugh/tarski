<?php
/*
Template Name: Links
*/

get_header(); ?>



<?php if (have_posts()) { while(have_posts()) { the_post(); ?>

	<div class="primary entry">
		<div class="meta">
			<h1 class="title"><?php the_title(); ?></h1>
			<?php edit_post_link(__('edit page','tarski'), '<p class="metadata">(', ')</p>'); ?>
		</div> <!-- /meta -->
		
		<?php if(get_the_content() != "") { ?>
			<div class="content">
				<?php the_content(); ?>
			</div> <!-- /content -->
		<?php } ?>
		<div class="bookmarks">
			<?php wp_list_bookmarks(array(
				'exclude_category' => get_tarski_option('nav_extlinkcat'),
				'category_before' => '',
				'category_after' => '',
				'title_before' => '<h3>',
				'title_after' => '</h3>',
				'show_images' => 0,
				'show_description' => 0
			)); ?>
		</div> <!-- /bookmarks -->

		<?php th_postend(); ?>
	</div> <!-- /primary -->
	
<?php } } ?>



<?php get_sidebar(); ?>



<?php get_footer(); ?>