<?php /*
Template Name: Tags
*/ ?>
<?php get_header(); ?>
<?php if (have_posts()) { while(have_posts()) { the_post(); ?>
	
	<div class="primary entry">
		<div class="meta">
			<h1 class="title"><?php the_title(); ?></h1>
			<?php edit_post_link(__('edit page','tarski'), '<p class="metadata">(', ')</p>'); ?>
		</div> <!-- /meta -->
		
		<div class="content">
			<?php if(get_the_content()) { ?>
				<?php the_content(); ?>
				
				<h3><?php _e('Tags','tarski'); ?></h3>
			
			<?php } ?>
			<?php if(function_exists('wp_tag_cloud')) { ?>
				<p class="tagcloud"><?php wp_tag_cloud(); ?></p>
			<?php } else { ?>
				<p><?php _e('Sorry, this version of WordPress doesn&#8217;t appear to support the tag cloud.','tarski'); ?></p>
			<?php } ?>
		</div> <!-- /content -->

		<?php th_postend(); ?>
	</div> <!-- /primary -->

<?php } } ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>