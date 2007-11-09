</div> <!-- /main content -->



<div id="footer">
	
	<div class="secondary">

		<?php th_fsidebar(); ?>

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

			if($posts) {
				include(TARSKIDISPLAY . '/recent_articles.php');
			}
		} ?>

	</div> <!-- /primary -->

	<div id="theme-info">
		<?php th_footer(); ?>
	</div> <!-- /theme-info -->

</div> <!-- /footer -->

</div><?php wp_footer(); ?></body></html>