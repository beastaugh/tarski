<div id="recent">
	<h3><?php _e('Recent Articles','tarski'); ?></h3>
	<ul>
		<?php while ($r->have_posts()) : $r->the_post(); ?>
		<li>
			<h4 class="recent-title"><a title="<?php _e('View this post', 'tarski'); ?>" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h4>
			<p class="recent-metadata"><?php
			echo tarski_date();
			if(!get_tarski_option('hide_categories')) {
				_e(' in ', 'tarski'); the_category(', ');
			} ?></p>
			<div class="recent-excerpt content"><?php tarski_excerpt(); ?></div>
		</li>
		<?php endwhile; ?>
	</ul>
</div> <!-- /recent -->