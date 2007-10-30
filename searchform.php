<div class="searchbox">
	<form method="get" id="searchform" action="<?php bloginfo('url'); ?>"><fieldset>
		<input type="text" value="<?php if(get_search_query()) { the_search_query(); } else { _e('Search this site','tarski'); } ?>" name="s" id="s" tabindex="21" />
		<input type="submit" id="searchsubmit" value="<?php _e('Search','tarski'); ?>" tabindex="22" />
	</fieldset></form>
</div>