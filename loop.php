<?php while(have_posts()) { the_post(); ?>
	
	<?php if(get_tarski_option('asidescategory') && in_category(get_tarski_option('asidescategory'))) { // Aside loop ?>
		
		<div class="aside hentry" id="p-<?php the_ID(); ?>">
			
			<div class="content entry-content"><?php the_content(__('Read the rest of this entry &raquo;','tarski')); ?></div>
			
			<p class="meta"><span class="date updated"><?php the_time(get_option('date_format')); ?></span><?php tarski_author_posts_link(); ?> | <a class="comments-link" rel="bookmark" href="<?php the_permalink(); ?>"><?php tarski_asides_permalink_text(); ?></a><?php edit_post_link(__('edit','tarski'), ' (', ')'); ?></p>
			
			<?php th_postend(); ?>
			
		</div>
	
	
	<?php } else { // Non-Aside loop ?>
	
	
		<div class="entry hentry">
			
			<div class="meta">
				<h2 class="title entry-title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e('Permanent link to ','tarski'); the_title(); ?>"><?php the_title(); ?></a></h2>
				<p class="metadata"><?php echo '<span class="date updated">'. get_the_time(get_option('date_format')) . '</span>';
				tarski_post_categories_link();
				tarski_author_posts_link();
				tarski_comments_link();
				edit_post_link(__('edit', 'tarski'),' <span class="edit">(',')</span>'); ?></p>
			</div>
			
			<div class="content entry-content">
				<?php the_content(__('Read the rest of this entry &raquo;','tarski')); ?>
			</div>
			
			<?php th_postend(); ?>
			
		</div>
	
		
	<?php } ?>
	
<?php } // End entry loop ?>

<?php th_posts_nav(); ?>