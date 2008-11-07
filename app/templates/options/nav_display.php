<?php

$pages = &get_pages('sort_column=post_parent,menu_order');

if($pages) {
	echo '<p>'. __('Pages selected here will display in your navbar.','tarski'). "</p>\n";
	echo tarski_navbar_select($pages);
	echo '<input type="hidden" id="opt-collapsed-pages" name="collapsed_pages" value="' . get_tarski_option('collapsed_pages') . '" />' . "\n\n";			
	echo '<p class="tip">' . __('To change the order in which they appear, edit the &#8216;Page Order&#8217; value on each page.','tarski') . "</p>\n";
	
} else {
	echo '<p>' . __('There are no pages to select navbar items from.','tarski') . "</p>\n";
} ?>