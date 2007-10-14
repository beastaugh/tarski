<?php get_header(); ?>



<div class="primary entry">

	<div class="meta">
		<h1 class="title"><?php _e('Error 404','tarski'); ?></h1>
	</div>

	<div class="content">
		<?php tarski_404_content(); ?>
	</div>

	<?php th_postend(); ?>

</div> <!-- /primary -->



<div class="secondary">
	<?php tarski_searchform(); ?>
</div>



<?php get_footer(); ?>