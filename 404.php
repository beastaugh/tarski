<?php get_header();
@include(TEMPLATEPATH . '/constants.php'); ?>
<div class="primary entry">
	<div class="meta">
		<h1 class="title"><?php _e('Error 404','tarski'); ?></h1>
	</div>
	<div class="content">
		<?php if($errorPageInclude) {
			echo $errorPageInclude;
		} else {
			echo '<p>' . __('The page you are looking for does not exist; it may have been moved, or removed altogether. You might want to try the search function or return to the ','tarski') . '<a href="' . get_settings('home') . '">' . __('front page','tarski') . '</a>' . __('.','tarski') . "</p>\n";
		} ?>
	</div>
</div> <!-- close primary content div -->
<?php get_footer(); ?>