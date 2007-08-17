<?php
/*
Template Name: Archives
*/
@include(TEMPLATEPATH . '/constants.php');
get_header(); ?>
	<div class="primary-span archive">
		<div class="meta">
			<h1 class="title"><?php _e('Archives', 'tarski'); ?></h1>
		</div>
	</div> <!-- /page header -->
<?php if(function_exists(srg_clean_archives)) { ?>
	<div class="primary">
		<h3><?php _e('Monthly Archives', 'tarski'); ?></h3>

		<ul class="archivelist xoxo">
		<?php srg_clean_archives(); ?>
		</ul>
		<?php th_postend(); ?>
	</div> <!-- /primary -->
<?php } else { ?>
	<div class="primary">
		<h3><?php _e('Monthly Archives', 'tarski'); ?></h3>

		<ul class="archivelist xoxo">
		<?php get_archives('monthly', '', 'html', '', '', 'TRUE'); ?>
		</ul>
		<?php th_postend(); ?>
	</div> <!-- /primary -->
<?php } ?>
	<div class="secondary">
	<?php if(!get_tarski_option('hide_categories')) { ?>
		<h3><?php _e('Category Archives', 'tarski'); ?></h3>
		<ul class="archivelist xoxo">
		<?php wp_list_cats('sort_column=name&sort_order=desc'); ?>
		</ul>
	<?php } ?>
	<?php th_sidebar(); ?>
	</div> <!-- /secondary -->
<?php get_footer(); ?>