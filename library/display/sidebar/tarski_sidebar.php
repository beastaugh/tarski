<?php if(get_tarski_option('sidebar_custom')) { // Blurb ?>
	
	<div class="content">
		<?php tarski_sidebar_custom(); ?>
	</div>

<?php } ?>
<?php if(get_tarski_option('sidebar_pages')) { // Pages ?>

	<h3><?php _e('Pages','tarski'); ?></h3>
	<ul class="navlist xoxo">
		<?php wp_list_pages('sort_column=post_title&title_li='); ?>
	</ul>

<?php } ?>
<?php if(get_tarski_option('sidebar_links')) { // Links ?>

	<div class="bookmarks xoxo">
		<?php wp_list_bookmarks(tarski_sidebar_links()); ?>
	</div>

<?php } ?>

<?php wp_meta(); ?>