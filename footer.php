</div> <!-- /main content -->



<div id="footer">
	
	<div class="secondary">
		<?php if((get_tarski_option('sidebar_type') == 'widgets') && dynamic_sidebar(__('Footer Widgets', 'tarski')) { ?>

			<div class="widgets">
				<?php dynamic_sidebar(__('Footer Widgets', 'tarski')); ?>
			</div>

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