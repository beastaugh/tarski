<?php
/*
Template Name: Archives
*/

get_header(); ?>



<?php if (have_posts()) { while (have_posts()) { the_post(); ?>
	<div class="primary-span entry">
		<div class="meta">
			<h1 class="title"><?php the_title(); ?></h1>
			<?php edit_post_link(__('edit page','tarski'), '<p class="metadata">(', ')</p>'); ?>
		</div> <!-- /meta -->
		
	<?php if(get_the_content() != "") { ?>
		<div class="content">
			<?php the_content(); ?>
		</div> <!-- /content -->
	<?php } ?>
	</div> <!-- /page header -->

	<div class="primary">
		<h3><?php _e('Monthly Archives', 'tarski'); ?></h3>

		<ul class="archivelist xoxo">
			<?php get_archives('monthly', '', 'html', '', '', 'TRUE'); ?>
		</ul>
		<?php th_postend(); ?>
	</div> <!-- /primary -->
<?php } } ?>

	<div class="secondary">
	<?php if(get_tarski_option('show_categories')) { ?>
		<h3><?php _e('Category Archives', 'tarski'); ?></h3>
		<ul class="archivelist xoxo">
			<?php wp_list_cats('sort_column=name&sort_order=desc'); ?>
		</ul>
	<?php } ?>
	<?php th_sidebar(); ?>
	</div> <!-- /secondary -->
	
	

<?php get_footer(); ?>