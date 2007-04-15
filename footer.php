</div> <!-- /main content -->



<div id="footer">
	
	<div class="secondary">
	
	<?php if(function_exists('dynamic_sidebar')) { echo "<div class=\"widgets\">\n"; } ?>

	<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar(__('Footer Widgets', 'tarski')) ) :  // Footer widgets ?>
	
		<?php if(!is_search()) { include(TEMPLATEPATH . '/library/searchform.php'); } ?>

	<?php endif; // end widgets if ?>

	<?php if(function_exists('dynamic_sidebar')) { echo "</div>\n"; } ?>

	</div> <!-- /secondary -->


	<div class="primary">
<?php if(get_tarski_option('blurb')) { // Footer blurb ?>
		<div class="content">
			<?php echo wpautop(wptexturize(stripslashes(get_tarski_option('blurb')))); ?>
		</div> <!-- /blurb -->
<?php } // end footer blurb ?>
		
<?php // Recent articles
if(get_tarski_option('footer_recent')) {
	
	if (is_page('archives')) {
	
	} else {
		if (is_home()) {
			$posts = get_posts('numberposts=5&offset=' . count($posts));
			// offset the homepage by # of posts already displayed
		} else {
			$posts = get_posts('numberposts=5&offset=0');
		}
		
		if($posts) { ?>
		<div id="recent"><h3><?php _e('Recent Articles', 'tarski'); ?></h3>
			<ul>
<?php foreach ($posts as $post) { ?>
				<li><h4 class="recent-title"><a title="<?php _e('View this post', 'tarski'); ?>" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h4>
				<p class="recent-metadata"><?php echo tarski_date(); if(!get_tarski_option('hide_categories')) { _e(' in ', 'tarski'); the_category(', '); } ?></p>
				<p class="recent-excerpt content"><?php
				$excerpt = tarski_excerpt(35, '', 'the_content', FALSE, '', FALSE, 1, TRUE);
				echo strip_tags($excerpt); ?></p></li>
<?php } ?>
			</ul>
		</div> <!-- /recent -->
<?php } } } // end recent articles ?>

	</div> <!-- /primary -->


	<div id="theme-info">
		<div class="primary content">
			<p><?php if(detectWPMU()) { $current_site = get_current_site(); } _e('Powered by <a href="http://wordpress.org/">WordPress</a> and <a href="http://tarskitheme.com/">Tarski</a>', 'tarski'); ?><?php if(detectWPMU()) { echo ' | ' . __('Hosted by ', 'tarski') . '<a href="http://' . $current_site->domain . $current_site->path . '">' . $current_site->site_name . '</a>'; } ?></p>
		</div>
		<div class="secondary">
			<p><a class="feed" href="<?php echo get_bloginfo_rss('rss2_url'); ?>"><?php _e('Subscribe to feed', 'tarski'); ?></a></p>
		</div>
		<div id="footer-include">
			<?php @include(TEMPLATEPATH . '/constants.php'); echo $footerInclude; ?>
		</div>
	</div> <!-- /theme-info -->

</div> <!-- /footer -->
<?php global $installedVersion; ?>
<!--
This website is powered by WordPress and Tarski <?php echo $installedVersion; ?>

You can download Tarski from http://tarskitheme.com
-->
</div><?php wp_footer(); ?></body></html>