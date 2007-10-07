<?php get_header(); ?>



<div class="primary">
	<?php if(have_posts()) { // Gets it all going ?>

		<div class="archive">
			<?php if(is_category()) { // Category header ?>
				<div class="meta">
					<h1><?php echo single_cat_title(); ?></h1>
				</div>
				<div class="content">
					<?php if(trim(strip_tags(category_description()))) { ?>
						<?php echo category_description(); ?>
					<?php } else { ?>
						<p><?php echo __('You are currently browsing the archive for the ','tarski'). '<strong>'; single_cat_title(); echo '</strong>'. __(' category.','tarski'); ?></p>
					<?php } ?>
				</div>
				
			<?php } elseif(is_author()) { // Author header ?>

				<div class="meta">
					<h1 class="title"><?php echo __('Articles by ','tarski'). the_archive_author_displayname(); ?></h1>
				</div>
				<div class="content">
					<?php if(the_archive_author_description()) { ?>
						<?php echo wpautop(wptexturize(stripslashes(the_archive_author_description()))); ?>
					<?php } else { ?>
						<p><?php echo __('You are currently browsing ','tarski'). '<strong>'. the_archive_author_displayname(). '</strong>'. __('&#8217;s articles.','tarski'); ?></p>
					<?php } ?>
				</div>

			<?php } elseif(is_day()) { // Daily archive header ?>

				<div class="meta">
					<h1 class="title"><?php echo tarski_date(); ?></h1>
				</div>
				<div class="content">
					<p><?php _e('You are currently browsing the daily archive for ','tarski'); echo '<strong>' . tarski_date() . '</strong>.'; ?></p>
				</div>

			<?php } elseif(is_month()) { // Monthly archive header ?>

				<div class="meta">
					<h1 class="title"><?php the_time('F Y'); ?></h1>
				</div>
				<div class="content">
					<p><?php _e('You are currently browsing the monthly archive for ','tarski'); echo '<strong>'; the_time('F Y'); echo '</strong>.'; ?></p>
				</div>

			<?php } elseif(is_year()) { // Yearly archive header ?>

				<div class="meta">
					<h1 class="title"><?php the_time('Y'); ?></h1>
				</div>
				<div class="content">
					<p><?php echo __('You are currently browsing the yearly archive for ','tarski'). '<strong>'; the_time('Y'); echo '</strong>.'; ?></p>
				</div>

			<?php } elseif(function_exists('is_tag')) { if(is_tag()) { // Tag archive header ?>

				<div class="meta">
					<h1 class="title"><?php single_tag_title(); ?></h1>
				</div>
				<div class="content">
					<p><?php echo __('You are currently browsing articles tagged ','tarski'). '<strong>'. single_tag_title('',false). '</strong>'. __('.','tarski'); ?></p>
				</div>

			<?php } } ?>
		</div> <!-- /archive -->
		
		
		<?php include(TEMPLATEPATH.'/loop.php'); ?>


		<?php tarski_next_prev_pages(); ?>
		
		
	<?php } else { ?>
		
		<?php include(TARSKIDISPLAY . "/no_posts.php"); ?>
		
	<?php } // End if posts ?>
</div> <!-- /primary -->



<?php get_sidebar(); ?>



<?php get_footer(); ?>