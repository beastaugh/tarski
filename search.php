<?php get_header(); ?>

<div class="primary">
	<?php if(have_posts()) { // Gets it all going ?>
		
		
		<div class="archive">
			<div class="meta">
				<h1 class="title"><?php _e('Search Results','tarski'); ?></h1>
			</div>
			<div class="content">
				<p><?php echo sprintf(__('Your search for %s returned the following results.','tarski'), '<strong>' . get_search_query() . '</strong>'); ?></p>
			</div>
		</div> <!-- /archive -->
		
		<?php include(TEMPLATEPATH.'/loop.php'); ?>
		
		<?php tarski_posts_nav_link(); ?>
		
		
		
	<?php } else { ?>
		
		
		<div class="entry">
			<div class="meta">
				<h1 class="title"><?php _e('No results','tarski'); ?></h1>
			</div>
			<div class="content">
				<p><?php echo sprintf( __('Your search for %1$s returned no results. Try returning to the %2$s.','tarski'), '<strong>'.get_search_query().'</strong>', '<a href="'. get_bloginfo('url'). '">'. __('front page','tarski'). '</a>' ); ?></p>
			</div>
		</div> <!-- /entry -->
		
		
	<?php } ?>
</div> <!-- /primary -->



<div class="secondary">
	<?php tarski_searchform(); ?>
</div>



<?php get_footer(); ?>