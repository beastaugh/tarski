<?php // loop.php - This file does the heavy lifting

$the_author = $wpdb->get_var("SELECT display_name FROM $wpdb->users WHERE ID = '$post->post_author'"); // Author name for author archive pages


if (have_posts()) { // Gets it all going


if (is_single()) { // Single entry ?>
<?php
$prev_post = '';
$next_post = '';
$prev_post = tarski_get_output("previous_post_link('&laquo; %link');");
$next_post = tarski_get_output("next_post_link('%link &raquo;');");

if($prev_post && $next_post) {
	echo '<p class="articlenav primary-span">' . $prev_post . ' &nbsp;&bull;&nbsp; ' . $next_post . '</p>';
} elseif($prev_post || $next_post) {
	echo '<p class="articlenav primary-span">' . $prev_post . $next_post . '</p>';
} ?>
<div class="primary">
<?php while(have_posts()) { the_post(); ?>
<?php $trackbackLink = trackback_url(false); ?>
	<div class="entry">
		<div class="meta">
			<h1 class="title"><?php the_title(); ?></h1>
			<p class="metadata"><?php echo tarski_date();
			if(!get_tarski_option('hide_categories')) { _e(' in ', 'tarski'); the_category(', '); }
			if($multipleAuthors) { _e(' by ', 'tarski'); the_author_posts_link(); }
			edit_post_link(__('edit', 'tarski'),' (',')'); ?></p>
		</div>
		<div class="content">
			<?php the_content(); ?>
			<?php if(function_exists('UTW_ShowTagsForCurrentPost')) { // UTW tags
			echo '<p class="tagdata"><strong>'; _e('Tags:', 'tarski'); echo '</strong> '; UTW_ShowTagsForCurrentPost('commalist'); echo '</p>'; } ?>
			<?php // WP 2.2 built-in tagging, commented out for now
			// if(function_exists('the_tags')) { the_tags('<p class="tagdata"><strong>' . __('Tags','tarski') . '</strong>', ', ', '</p>'); } ?> 
		</div>
		<?php link_pages_without_spaces(); ?>
		<?php th_postend(); ?>
	</div>
<?php } // End entry loop ?>
</div>



<?php } elseif(is_page()) { // Page (default template) ?>
<div class="primary">
<?php while(have_posts()) { the_post(); ?>
	<div class="entry">
		<div class="meta">
			<h1 class="title"><?php the_title(); ?></h1>
			<?php edit_post_link(__('edit page', 'tarski'), '<p class="metadata">(', ')</p>'); ?>
		</div>
		<div class="content">
			<?php the_content(); ?>
		</div>
		<?php link_pages_without_spaces(); ?>
		<?php th_postend(); ?>
	</div>
<?php } // End entry loop ?>
</div>



<?php } elseif (is_home() || is_archive() || is_search() || is_tag()) { // Everything else ?>
<div class="primary">
<?php if(is_home()) { // Home page header
// For use when you want something at the top of your home page
} elseif(is_category()) { // Category header ?>
	<div class="archive">
		<div class="meta">
			<h1><?php echo single_cat_title(); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('You are currently browsing the archive for the ', 'tarski'); echo '<strong>'; single_cat_title(); echo '</strong>'; _e(' category.', 'tarski'); ?></p>
		</div>
	</div>
<?php } elseif(is_author()) { // Author header ?>
	<div class="archive">
		<div class="meta">
			<h1 class="title"><?php echo 'Articles by ' . $the_author; ?></h1>
		</div>
		<div class="content">
			<p><?php _e('You are currently browsing ', 'tarski'); echo '<strong>' . $the_author . '</strong>'; _e('&#8217;s articles.', 'tarski'); ?></p>
		</div>
	</div>
<?php } elseif(is_day()) { // Daily archive header ?>
	<div class="archive">
		<div class="meta">
			<h1 class="title"><?php echo tarski_date(); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('You are currently browsing the daily archive for ', 'tarski'); echo '<strong>' . tarski_date() . '</strong>.'; ?></p>
		</div>
	</div>
<?php } elseif(is_month()) { // Monthly archive header ?>
	<div class="archive">
		<div class="meta">
			<h1 class="title"><?php the_time('F Y'); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('You are currently browsing the monthly archive for ', 'tarski'); echo '<strong>'; the_time('F Y'); echo '</strong>.'; ?></p>
		</div>
	</div>
<?php } elseif(is_year()) { // Yearly archive header ?>
	<div class="archive">
		<div class="meta">
			<h1 class="title"><?php the_time('Y'); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('You are currently browsing the yearly archive for ', 'tarski'); echo '<strong>'; the_time('Y'); echo '</strong>.'; ?></p>
		</div>
	</div>
<?php } elseif(is_search()) { // Search results header ?>
	<div class="archive">
		<div class="meta">
			<h1 class="title"><?php _e('Search Results', 'tarski'); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('Your search for ', 'tarski'); echo '<strong>' . wp_specialchars($s, 1) . '</strong> '; _e('returned the following results.', 'tarski'); ?></p>
		</div>
	</div>
<?php } elseif(function_exists('is_tag')) { if(is_tag()) { // Tag archive header ?>
	<div class="archive">
		<div class="meta">
			<h1 class="title"><?php
			$format = array(
				'pre' => '',
				'single' => '%tagdisplay%',
				'first' => '%tagdisplay%, ',
				'default' => '%tagdisplay%, ',
				'last' => '%tagdisplay%',
				'none' => '',
				'post' => ''
			);
			UTW_ShowCurrentTagSet('tagsetcommalist', $format); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('You are currently browsing articles tagged ', 'tarski'); echo '<strong>'; UTW_ShowCurrentTagSet('tagsetcommalist', $format); echo '</strong>'; _e('.', 'tarski');?></p>
		</div>
	</div>
<?php } } // Closes headers ?>



<?php // General loop including Asides goes here
if(!is_home() && !get_tarski_option('use_pages')) { $posts = query_posts($query_string . '&nopaging=1'); }
while (have_posts()) { the_post(); ?>
<?php if(get_tarski_option('asidescategory') != 0 && in_category(get_tarski_option('asidescategory'))) { // Aside loop ?>
	<div class="aside" id="p-<?php the_ID(); ?>">
		<div class="content"><?php the_content(__('Read the rest of this entry &raquo;', 'tarski')); ?></div>
		<p class="meta"><?php echo tarski_date(); if($multipleAuthors) { _e(' by ', 'tarski'); the_author_posts_link(); } echo ' | '; ?><a href="<?php the_permalink(); ?>"><?php if($post->comment_status == 'open' || $post->comment_count > 0) { comments_number(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski')); } else { _e('Permalink', 'tarski'); } ?></a><?php edit_post_link(__('edit', 'tarski'), ' (', ')'); ?></p>
	</div>
<?php } else { // Non-Aside loop ?>
	<div class="entry">
		<div class="meta">
			<h2 class="title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e('Permanent Link to ', 'tarski'); the_title(); ?>"><?php the_title(); ?></a></h2>
			<p class="metadata"><?php echo tarski_date();
			if(!get_tarski_option('hide_categories')) { _e(' in ', 'tarski'); the_category(', '); }
			if($multipleAuthors) { _e(' by ', 'tarski'); the_author_posts_link(); }
			if($post->comment_status == 'open' || $post->comment_count > 0) { echo ' | '; comments_popup_link(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski'), '', __('Comments closed', 'tarski')); }
			edit_post_link(__('edit', 'tarski'),' (',')'); ?></p>
		</div>
		<div class="content">
			<?php the_content(__('Read the rest of this entry &raquo;', 'tarski')); ?>
		</div>
		<?php link_pages_without_spaces(); ?>
	</div>
<?php } th_postend(); } // End entry loop
 
global $wp_query;
$wp_query->is_paged = true;

/* Experimental code, not currently active (ought to work with WP trunk 2.2+)
if(is_paged() && get_tarski_option('use_pages')) {
	echo "<p class=\"pagination\">\n";
	$prev_page = '';
	$next_page = '';
	if(is_search()) {
		$prev_page = '<a href="' . clean_url(get_previous_posts_page_link()) . '">&laquo; ' . __('Previous results','tarski') . '</a>';
		$next_page = '<a href="' . clean_url(get_next_posts_page_link()) . '">' . __('More results','tarski') . ' &raquo;</a>';
	} else {
		$prev_page = '<a href="' . clean_url(get_previous_posts_page_link()) . '">&laquo; ' . __('Older entries','tarski') . '</a>';
		$next_page = '<a href="' . clean_url(get_next_posts_page_link()) . '">' . __('Newer entries','tarski') . ' &raquo;</a>';
	}
	if(strip_tags($prev_page) && strip_tags($next_page)) {
		echo $prev_page . " &sect; " . $next_page;
	} else {
		echo $prev_page . $next_page;
	}
	echo "</p>\n";
}
*/

// Current, hackish code
if(is_paged() && get_tarski_option('use_pages')) {
	echo "<p class=\"pagination\">\n";
	if(is_search() || $_GET['s']) {
		$prev_page_text = __('Previous results','tarski');
		$next_page_text = __('More results','tarski');
		$prev_page = '';
		$next_page = '';
		$prev_page = tarski_get_output("posts_nav_link('','&laquo; $prev_page_text', '');");
		$next_page = tarski_get_output("posts_nav_link('','','$next_page_text &raquo; ');");
		
		if(strip_tags($prev_page) && strip_tags($next_page)) {
			echo $prev_page . " &sect; " . $next_page;
		} else {
			echo $prev_page . $next_page;
		}
	} else {
		$prev_page_text = __('Older entries','tarski');
		$next_page_text = __('Newer entries','tarski');
		$prev_page = '';
		$next_page = '';
		$prev_page = tarski_get_output("posts_nav_link('','','&laquo; $prev_page_text');");
		$next_page = tarski_get_output("posts_nav_link('','$next_page_text &raquo;','');");
		
		if(strip_tags($prev_page) && strip_tags($next_page)) {
			echo $prev_page . " &sect; " . $next_page;
		} else {
			echo $prev_page . $next_page;
		}

	} echo "</p>\n";
} ?>
</div>
<?php } // Closes 'Everything else' section ?>



<?php } // If there are no posts...



elseif (is_search()) { // No results for search ?>
<div class="primary">
	<div class="entry">
		<div class="meta">
			<h1 class="title"><?php _e('No results', 'tarski'); ?></h1>
		</div>
		<div class="content">
			<p><?php echo __('Your search for ', 'tarski') . '<strong>' . wp_specialchars($s, 1) . '</strong>' . __(' returned no results. Try returning to the ', 'tarski') . '<a href="' . get_settings('home') . '">' . __('front page', 'tarski') . '</a>' . __('.', 'tarski'); ?></p>
		</div>
	</div>
</div>



<?php } elseif(function_exists('is_tag')) { if(is_tag()) { // No results for tag ?>
<div class="primary">
	<div class="entry">
		<div class="meta">
			<h1 class="title"><?php
			$format = array(
				'pre' => '',
				'single' => '%tagdisplay%',
				'first' => '%tagdisplay%, ',
				'default' => '%tagdisplay%, ',
				'last' => '%tagdisplay%',
				'none' => '',
				'post' => ''
			);
			UTW_ShowCurrentTagSet('tagsetcommalist', $format); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('There are no articles tagged ', 'tarski'); echo '<strong>'; UTW_ShowCurrentTagSet('tagsetcommalist', $format); echo '</strong>.'; _e('Try returning to the ', 'tarski'); echo '<a href="' . get_settings('home') . '">'; _e('front page', 'tarski'); echo '</a>'; _e('.', 'tarski'); ?></p>
		</div>
	</div>
</div>



<?php } } else { // No dice... ?>
<div class="primary">
	<div class="entry">
		<div class="meta">
			<h1 class="title"><?php _e('Sorry', 'tarski'); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('Looks like there&#8127;s nothing here, sorry. You might want to try the search function. Alternatively, return to the ', 'tarski'); ?><a href="<?php echo get_settings('home'); ?>"><?php _e('front page', 'tarski'); ?></a><?php _e('.', 'tarski'); ?></p>
		</div>
	</div>
</div>



<?php } // That's all folks! ?>