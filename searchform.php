<?php global $s; @include(TEMPLATEPATH . '/constants.php'); if($searchTopInclude) { echo $searchTopInclude; } ?>

<div class="searchbox">
	<form method="get" id="searchform" action="<?php echo get_bloginfo('url') . '/'; ?>"><fieldset>
		<input type="text" value="<?php if($s) { echo wp_specialchars($s, 1); } else { _e('Search this site','tarski'); } ?>" name="s" id="s" tabindex="21" />
		<input type="submit" id="searchsubmit" value="<?php _e('Search','tarski'); ?>" tabindex="22" />
	</fieldset></form>
</div>

<?php if($searchBottomInclude) { echo $searchBottomInclude; } ?>