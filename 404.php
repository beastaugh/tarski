<?php get_header(); ?>
<div class="primary-span entry">
	<div class="meta">
		<h1 class="title"><?php _e('Error 404','tarski'); ?></h1>
	</div>
	<div class="content">
		<?php tarski_404_content(); ?>
	</div>
	<?php th_postend(); ?>
</div> <!-- close primary content div -->
<?php get_footer(); ?>