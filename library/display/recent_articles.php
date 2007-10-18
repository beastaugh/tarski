<div id="recent">
	<h3><?php _e('Recent Articles','tarski'); ?></h3>
	<ul>
		<?php foreach($posts as $post) { ?>
			<li>
				<h4 class="recent-title"><a title="<?php _e('View this post', 'tarski'); ?>" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h4>
				<p class="recent-metadata"><?php echo tarski_date(); if(!get_tarski_option('hide_categories')) { _e(' in ', 'tarski'); the_category(', '); } ?></p>
				<p class="recent-excerpt content"><?php echo strip_tags(tarski_excerpt(35, '', 'the_content', false, '', false, 1, true); ?></p>
			</li>
		<?php } ?>
	</ul>
</div> <!-- /recent -->