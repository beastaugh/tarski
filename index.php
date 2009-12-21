<?php get_header(); ?>

<div class="primary<?php if (!(is_single() || is_page())) echo ' posts'; ?>">

	<?php if (have_posts()) { ?>
	
		<?php if (is_single() || is_page()) { // Single entries and pages ?>
		
			<?php while (have_posts()) { the_post(); ?>
				
				<div <?php post_class('entry'); ?>>
					
					<div class="meta">
						<h1 class="title entry-title"><?php the_title(); ?></h1>
						<?php if (is_attachment()) { ?>
							<p class="metadata"><?php
								echo '<span class="date updated">' . get_the_time(get_option('date_format')) . '</span>';
								edit_post_link(__('edit','tarski'),' <span class="edit">(',')</span>');
							?></p>
						<?php } elseif (is_single()) { ?>
							<p class="metadata"><?php
								echo '<span class="date updated">' . get_the_time(get_option('date_format')) . '</span>';
								tarski_post_categories_link();
								tarski_author_posts_link();
								tarski_comments_link();
								edit_post_link(__('edit','tarski'),' <span class="edit">(',')</span>');
							?></p>
						<?php } else { ?>
							<?php edit_post_link(__('edit page','tarski'), '<p class="metadata"><span class="edit">(', ')</span></p>'); ?>
						<?php } ?>
					</div>
					
					<div class="content clearfix">
                        <?php echo tarski_post_thumbnail(); ?>
						<?php the_content(); ?>
					</div>

					<?php th_postend(); ?>
					
				</div> <!-- /entry -->
				
			<?php } // End entry loop ?>
	
		<?php } else { ?>
		
			<?php include(TEMPLATEPATH . '/loop.php'); ?>

		<?php } // End loop types ?>
	
	<?php } else { // If no posts ?>
		
		<?php include(TARSKIDISPLAY . "/no_posts.php"); ?>
	
	<?php } // End loop ?>


	<?php if (!is_attachment() && (is_single() || is_page())) comments_template(); ?>

</div>
	
<?php get_sidebar(); ?>

<?php get_footer(); ?>