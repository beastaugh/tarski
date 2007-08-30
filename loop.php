<?php // loop.php - This file does the heavy lifting

global $s; // Search string

if(have_posts()) { // Gets it all going

if(is_single()) { // Single entry ?>
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
			<p class="metadata"><?php echo '<span class="date">'. tarski_date(). '</span>';
			if(!get_tarski_option('hide_categories')) { echo __(' in ', 'tarski'). '<span class="categories">'; the_category(', '); echo '</span>'; }
			if($multipleAuthors) { _e(' by ', 'tarski'); the_author_posts_link(); }
			edit_post_link(__('edit', 'tarski'),' <span class="edit">(',')</span>'); ?></p>
		</div>
		<div class="content">
			<?php the_content(); ?>
			<?php if(function_exists('the_tags')) { the_tags('<p class="tagdata"><strong>' . __('Tags','tarski') . ':</strong> ', ', ', '</p>'); } ?> 
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
			<?php edit_post_link(__('edit page', 'tarski'), '<p class="metadata"><span class="edit">(', ')</span></p>'); ?>
		</div>
		<div class="content">
			<?php the_content(); ?>
		</div>
		<?php link_pages_without_spaces(); ?>
		<?php th_postend(); ?>
	</div>
<?php } // End entry loop ?>
</div>



<?php } elseif(is_home() || is_archive() || is_search()) { // Everything else ?>
<div class="primary">

<?php if(is_archive() || is_search()) { ?>
	<div class="archive">
	<?php if(is_category()) { // Category header ?>
		<div class="meta">
			<h1><?php echo single_cat_title(); ?></h1>
		</div>
		<div class="content">
			<p><?php if(category_description()) {
				echo category_description();
			} else {
				echo __('You are currently browsing the archive for the ','tarski'). '<strong>'; single_cat_title(); echo '</strong>'. __(' category.','tarski');
			} ?></p>
		</div>
	<?php } elseif(is_author()) { // Author header ?>
		<div class="meta">
			<h1 class="title"><?php echo __('Articles by ','tarski'). the_archive_author_displayname(); ?></h1>
		</div>
		<div class="content">
			<?php if(the_archive_author_description()) { ?>
				<?php echo wpautop(wptexturize(stripslashes(the_archive_author_description()))); ?>
			<?php } else { ?>
				<p><?php echo __('You are currently browsing ','tarski'). '<strong>'. the_archive_author_displayname(). '</strong>'. __('&#8217;s articles.','tarski'); ?></p>
			<?php } ?>
		</div>
	<?php } elseif(is_day()) { // Daily archive header ?>
		<div class="meta">
			<h1 class="title"><?php echo tarski_date(); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('You are currently browsing the daily archive for ','tarski'); echo '<strong>' . tarski_date() . '</strong>.'; ?></p>
		</div>
	<?php } elseif(is_month()) { // Monthly archive header ?>
		<div class="meta">
			<h1 class="title"><?php the_time('F Y'); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('You are currently browsing the monthly archive for ','tarski'); echo '<strong>'; the_time('F Y'); echo '</strong>.'; ?></p>
		</div>
	<?php } elseif(is_year()) { // Yearly archive header ?>
		<div class="meta">
			<h1 class="title"><?php the_time('Y'); ?></h1>
		</div>
		<div class="content">
			<p><?php echo __('You are currently browsing the yearly archive for ','tarski'). '<strong>'; the_time('Y'); echo '</strong>.'; ?></p>
		</div>
	<?php } elseif(is_search()) { // Search results header ?>
		<div class="meta">
			<h1 class="title"><?php _e('Search Results','tarski'); ?></h1>
		</div>
		<div class="content">
			<p><?php echo __('Your search for ', 'tarski'). '<strong>'. attribute_escape(stripslashes($s)). '</strong> '. __('returned the following results.','tarski'); ?></p>
		</div>
	<?php } elseif(function_exists('is_tag')) { if(is_tag()) { // Tag archive header ?>
		<div class="meta">
			<h1 class="title"><?php single_tag_title(); ?></h1>
		</div>
		<div class="content">
			<p><?php echo __('You are currently browsing articles tagged ','tarski'). '<strong>'. single_tag_title('',false). '</strong>'. __('.','tarski'); ?></p>
		</div>
	<?php } } ?>
	</div>
<?php } // Closes headers ?>



<?php // General loop including Asides goes here
if(!is_home() && !get_tarski_option('use_pages')) { $posts = query_posts($query_string . '&nopaging=1'); }
while (have_posts()) { the_post(); ?>
<?php if(get_tarski_option('asidescategory') != 0 && in_category(get_tarski_option('asidescategory'))) { // Aside loop ?>
	<div class="aside" id="p-<?php the_ID(); ?>">
		<div class="content"><?php the_content(__('Read the rest of this entry &raquo;', 'tarski')); ?></div>
		<p class="meta"><span class="date"><?php echo tarski_date(); ?></span><?php if($multipleAuthors) { _e(' by ', 'tarski'); the_author_posts_link(); } ?> | <a class="comments-link" href="<?php the_permalink(); ?>"><?php if($post->comment_status == 'open' || $post->comment_count > 0) { comments_number(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski')); } else { _e('Permalink', 'tarski'); } ?></a><?php edit_post_link(__('edit', 'tarski'), ' (', ')'); ?></p>
	</div>
<?php } else { // Non-Aside loop ?>
	<div class="entry">
		<div class="meta">
			<h2 class="title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e('Permanent Link to ','tarski'); the_title(); ?>"><?php the_title(); ?></a></h2>
			<p class="metadata"><?php echo '<span class="date">'. tarski_date(). '</span>';
			if(!get_tarski_option('hide_categories')) { echo __(' in ','tarski'). '<span class="categories">'; the_category(', '); echo '</span>'; }
			if($multipleAuthors) { _e(' by ','tarski'); the_author_posts_link(); }
			if($post->comment_status == 'open' || $post->comment_count > 0) { echo ' | '; comments_popup_link(__('No comments', 'tarski'), __('1 comment', 'tarski'), '%' . __(' comments', 'tarski'), 'comments-link', __('Comments closed', 'tarski')); }
			edit_post_link(__('edit', 'tarski'),' <span class="edit">(',')</span>'); ?></p>
		</div>
		<div class="content">
			<?php the_content(__('Read the rest of this entry &raquo;', 'tarski')); ?>
		</div>
		<?php link_pages_without_spaces(); ?>
	</div>
<?php } th_postend(); } // End entry loop ?>
<?php tarski_next_previous(); ?>
</div> <!-- /primary -->
<?php } // Closes 'Everything else' section ?>



<?php } // If there are no posts...



elseif (is_search()) { // No results for search ?>
<div class="primary">
	<div class="entry">
		<div class="meta">
			<h1 class="title"><?php _e('No results', 'tarski'); ?></h1>
		</div>
		<div class="content">
			<p><?php echo __('Your search for ', 'tarski') . '<strong>' . attribute_escape(stripslashes($s)) . '</strong>' . __(' returned no results. Try returning to the ', 'tarski') . '<a href="' . get_settings('home') . '">' . __('front page', 'tarski') . '</a>' . __('.', 'tarski'); ?></p>
		</div>
	</div>
</div>



<?php } elseif(function_exists('is_tag')) { if(is_tag()) { // No results for tag ?>
<div class="primary">
	<div class="entry">
		<div class="meta">
			<h1 class="title"><?php single_tag_title(); ?></h1>
		</div>
		<div class="content">
			<p><?php echo __('There are no articles tagged ', 'tarski'). '<strong>'. single_tag_title('',false). '</strong>'. __('. Try returning to the ', 'tarski'). '<a href="'. get_settings('home'). '">'. __('front page', 'tarski'). '</a>'. __('.','tarski'); ?></p>
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