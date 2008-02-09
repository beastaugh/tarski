<div class="searchbox">
	<form method="get" id="searchform" action="<?php bloginfo('url'); ?>"><fieldset>
		<label for="s" id="searchlabel"><?php _e('Search this site', 'tarski'); ?></label>
		<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" tabindex="21" />
		<input type="submit" id="searchsubmit" value="<?php _e('Search','tarski'); ?>" tabindex="22" />
	</fieldset></form>
</div>