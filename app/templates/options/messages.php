<?php if (!get_tarski_option('deleted')) { if (isset($_GET['restored'])) { ?>
	<div class="updated fade below-h2"><p><?php printf(
		__('Tarski options have been restored. %s', 'tarski'),
		'<a href="' . user_trailingslashit(get_bloginfo('url')) . '">' . __('View site &rsaquo;','tarski') . '</a>'
	); ?></p></div>
<?php } elseif (isset($_GET['updated'])) { ?>
	<div class="updated fade below-h2"><p><?php printf(
		__('Tarski options have been updated. %s', 'tarski'),
		'<a href="' . user_trailingslashit(get_bloginfo('url')) . '">' . __('View site &rsaquo;','tarski') . '</a>'
	); ?></p></div>
<?php } } ?>
