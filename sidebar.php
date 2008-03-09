<div id="sidebar" class="secondary">

	<?php th_sidebar(); // The magical sidebar hook ?>
	<?php $wp_the_query->current_post--; setup_postdata($wp_query->next_post()); // Reset post data for comments ?>
	
</div>