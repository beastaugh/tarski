<?php get_header(); ?>



<div class="primary posts">
    <?php if(have_posts()) { // Gets it all going ?>
        
        
        <div class="archive">
            <div class="meta">
                <h1 class="title"><?php _e('Search Results','tarski'); ?></h1>
            </div>
            <div class="content">
                <p><?php printf( __('Your search for %s returned the following results.','tarski'), '<strong>' . esc_html(get_search_query()) . '</strong>' ); ?></p>
            </div>
        </div> <!-- /archive -->
        
        <?php get_template_part('app/templates/loop'); ?>
        
        
    <?php } else { ?>
        
        
        <div class="entry">
            <div class="meta">
                <h1 class="title"><?php _e('No results','tarski'); ?></h1>
            </div>
            <div class="content">
                <p><?php printf( __('Your search for %1$s returned no results. Try returning to the %2$s.','tarski'), '<strong>' . esc_html(get_search_query()) . '</strong>', '<a href="' . home_url() . '">' . __('front page','tarski') . '</a>' ); ?></p>
            </div>
        </div> <!-- /entry -->
        
        
    <?php } ?>
</div> <!-- /primary -->



<?php get_sidebar(); ?>



<?php get_footer(); ?>