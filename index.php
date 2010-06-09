<?php get_header(); ?>

<div class="primary<?php if (!(is_single() || is_page())) echo ' posts'; ?>">

	<?php if (have_posts()) { ?>
	
		<?php if (is_single() || is_page()) { // Single entries and pages ?>
		
			<?php while (have_posts()) { the_post(); ?>
				
				<div <?php post_class('entry'); ?>>
					
					<div class="meta">
						<h1 class="title entry-title"><?php the_title(); ?></h1>
						
						<?php echo th_post_metadata(); ?>
					</div>
					
					<div class="content clearfix">
                        <?php echo tarski_post_thumbnail(); ?>
						<?php the_content(); ?>
					</div>

					<?php th_postend(); ?>
					
				</div> <!-- /entry -->
				
			<?php } // End entry loop ?>
	
		<?php } else { ?>
		
			<?php get_template_part('app/templates/loop'); ?>

		<?php } // End loop types ?>
	
	<?php } else { // If no posts ?>
		
		<?php get_template_part('app/templates/no_posts'); ?>
	
	<?php } // End loop ?>


	<?php if (!is_attachment() && (is_single() || is_page())) comments_template(); ?>

</div>
	
<?php get_sidebar(); ?>

<?php get_footer(); ?>