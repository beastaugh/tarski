</div> <!-- /main content -->



<div id="footer">
	
	<div class="secondary">
	
	<?php if (function_exists('dynamic_sidebar')) { echo "<div class=\"widgets\">\n"; } ?>

	<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar(__('Footer Widgets', 'tarski')) ) :  // Footer widgets ?>
	
		<?php if (!is_search()) { include(TEMPLATEPATH . '/library/searchform.php'); } ?>

	<?php endif; // end widgets if ?>

	<?php if (function_exists('dynamic_sidebar')) { echo "</div>\n"; } ?>

	</div> <!-- /secondary -->


	<div class="primary">
<?php

// 'Recent Articles' block
if (is_page('archives')) {
	
} else {
	if (is_home()) {
		$posts = get_posts('numberposts=5&offset=' . count($posts));
		// offset the homepage by the number of posts already displayed
	} else {
		$posts = get_posts('numberposts=5&offset=0');
	}
	
	// change offset to however many articles are on front page
	if($posts) { ?>
		<div id="recent"><h3><?php _e('Recent Articles', 'tarski'); ?></h3>
			<ul>
<?php foreach ($posts as $post) { ?>
				<li><h4 class="recent-title"><a title="<?php _e('View this post', 'tarski'); ?>" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h4>
				<p class="recent-metadata"><?php echo tarski_date(); if(!get_option('tarski_hide_categories')) { _e(' in ', 'tarski'); the_category(', '); } ?></p>
				<p class="recent-excerpt content"><?php
				$excerpt = tarski_excerpt(35, '', 'the_content', FALSE, '', FALSE, 1, TRUE);
				echo strip_tags($excerpt); ?></p></li>
<?php } ?>
			</ul>
		</div> <!-- /recent -->
<?php } } // end 'Recent Articles' block ?>
			
<?php if(get_option('blurb')) { // 'About' block ?>
		<div class="content">
			<h3><?php _e('About', 'tarski'); ?></h3>
			<?php echo wpautop(wptexturize(stripslashes(get_option('blurb')))); ?>
		</div> <!-- /blurb -->
<?php } // end 'About' block ?>

	</div> <!-- /primary -->


	<div id="theme-info">
		<div class="primary content">
			<p><?php _e('Powered by <a href="http://wordpress.org/">WordPress</a> and <a href="http://tarskitheme.com/">Tarski</a>', 'tarski'); ?></p>
		</div>
		<div class="secondary">
			<p><a class="feed" href="<?php echo get_bloginfo_rss('rss2_url'); ?>"><?php _e('Subscribe to feed', 'tarski'); ?></a></p>
		</div>
		<div id="footer-include">
			<?php @include(TEMPLATEPATH . '/constants.php'); echo $footerInclude;
			// echo "Loaded in "; timer_stop(1); echo " seconds."; ?>
		</div>
	</div> <!-- /theme-info -->

</div> <!-- /footer -->
<?php global $installedVersion; ?>
<!--
This website is powered by WordPress and Tarski <?php echo $installedVersion; ?>

You can download Tarski from http://tarskitheme.com
-->
</div><?php wp_footer(); ?></body></html>