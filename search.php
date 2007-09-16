<?php get_header(); ?>

<div class="primary">
	<?php if(have_posts()) { // Gets it all going ?>
		
		
		<div class="archive">
			<div class="meta">
				<h1 class="title"><?php _e('Search Results','tarski'); ?></h1>
			</div>
			<div class="content">
				<p><?php echo __('Your search for ','tarski'). '<strong>'. attribute_escape(get_search_query()). '</strong> '. __('returned the following results.','tarski'); ?></p>
			</div>
		</div> <!-- /archive -->
		
		<?php include(TEMPLATEPATH.'/loop.php'); ?>
		
		<?php tarski_next_prev_pages(); ?>
		
		
		
	<?php } else { ?>
		
		
		<div class="entry">
			<div class="meta">
				<h1 class="title"><?php _e('No results','tarski'); ?></h1>
			</div>
			<div class="content">
				<p><?php echo __('Your search for ','tarski'). '<strong>'. attribute_escape(get_search_query()). '</strong>'. __(' returned no results. Try returning to the ','tarski'). '<a href="'. get_settings('home'). '">'. __('front page', 'tarski'). '</a>'. __('.','tarski'); ?></p>
			</div>
		</div> </div> <!-- /entry -->
		
		
	<?php } ?>
</div> <!-- /primary -->



<?php get_sidebar(); ?>



<?php get_footer(); ?>