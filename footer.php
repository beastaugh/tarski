</div> <!-- /main content -->



<div id="footer">
	
	<div class="secondary">
	<?php if(get_tarski_option('sidebar_type') == 'widgets') { ?>
		
		<?php if(function_exists('dynamic_sidebar')) { echo "<div class=\"widgets\">\n"; } ?>
		<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar(__('Footer Widgets', 'tarski')) ) :  // Footer widgets ?>
		<?php endif; // end widgets if ?>
		<?php if(function_exists('dynamic_sidebar')) { echo "</div>\n"; } ?>
	
	<?php } else { ?>

		<?php th_fsidebar(); ?>

	<?php } ?>
	</div> <!-- /secondary -->


	<div class="primary">
		
		<?php tarski_footer_blurb(); ?>
		
		<?php if(get_tarski_option('footer_recent') && !is_page('archives')) {
			if (is_home()) {
				$post_options = array('numberposts' => 5, 'offset' => count($posts));
				// offset the homepage by # of posts already displayed
			} else {
				$post_options = array('numberposts' => 5, 'offset' => 0);
			}

			$posts = get_posts($post_options);
			$excerpt = strip_tags(tarski_excerpt(35, '', 'the_content', false, '', false, 1, true));

			if($posts) {
				include(TARSKIDISPLAY . '/recent_articles.php');
			}
		} ?>

	</div> <!-- /primary -->

	<div id="theme-info">
		<?php th_footer(); ?>
	</div> <!-- /theme-info -->

</div> <!-- /footer -->
<!--
This website is powered by WordPress and Tarski <?php echo theme_version(); ?>

You can download Tarski from http://tarskitheme.com
-->
</div><?php wp_footer(); ?></body></html>