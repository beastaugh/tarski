<?php while (have_posts()) {
    the_post();
    if (has_post_format('aside') || (get_tarski_option('asidescategory') && in_category(get_tarski_option('asidescategory')))) { // Aside loop ?>
        <div <?php post_class('aside'); ?> id="p-<?php the_ID(); ?>">
            <div class="content entry-content clearfix">
                <?php if (!get_tarski_option('featured_header')) echo tarski_post_thumbnail(); ?>
                <?php the_content(__('Read the rest of this entry &raquo;','tarski')); ?>
            </div>
            
            <p class="meta"><span class="date updated"><?php the_time(get_option('date_format')); ?></span><?php echo tarski_author_posts_link(''); ?> | <a class="comments-link" rel="bookmark" href="<?php the_permalink(); ?>"><?php tarski_asides_permalink_text(); ?></a><?php edit_post_link(__('edit','tarski'), ' (', ')'); ?></p>
            
            <?php th_postend(); ?>
        </div>
    <?php } else { // Non-Aside loop ?>
        <div <?php post_class('entry'); ?>>
            
            <div class="meta">
                <h2 class="title entry-title" id="post-<?php the_ID(); ?>">
                    <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf(__('Permanent link to %s', 'tarski'), the_title_attribute(array('echo' => 0))); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>
                
                <?php echo th_post_metadata(); ?>
            </div>
            
            <div class="content entry-content clearfix">
                <?php if (!get_tarski_option('featured_header')) echo tarski_post_thumbnail(); ?>
                <?php the_content(__('Read the rest of this entry &raquo;','tarski')); ?>
            </div>
            
            <?php th_postend(); ?>
        </div>
<?php } } // End entry loop ?>

<?php th_posts_nav(); ?>
