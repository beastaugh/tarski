<?php $labelText = __('Search this site', 'tarski'); ?>
<div class="searchbox">
	<form method="get" id="searchform" action="<?php bloginfo('url'); ?>"><fieldset>
		<label for="s" id="searchlabel"><?php echo $labelText ?></label>
		<input type="text" placeholder="<?php echo $labelText ?>" value="<?php the_search_query(); ?>" name="s" id="s" />
		<input type="submit" id="searchsubmit" value="<?php _e('Search','tarski'); ?>" />
	</fieldset></form>
</div>
