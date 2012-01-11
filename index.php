<?php get_header(); ?>

<div class="primary<?php if (!is_singular()) echo ' posts'; ?>">
    <?php if (have_posts()) { ?>
    
        <?php if (is_singular()) { // Single entries and pages ?>
        
            <?php while (have_posts()) { the_post(); ?>
            
                <div <?php post_class('entry'); ?>>
                
                    <div class="meta">
                        <?php the_title('<h1 class="title entry-title">', '</h1>'); ?>
                        
                        <?php echo th_post_metadata(); ?>
                    </div>
                    
                    <div class="content clearfix">
                        <?php if (!get_tarski_option('featured_header')) echo tarski_post_thumbnail(); ?>
                        <?php the_content(); ?>
                    </div>
                    
                    <?php th_postend(); ?>
                
                </div> <!-- /entry -->
            
            <?php } // End entry loop ?>
        
        <?php } else { ?>
        
            <?php get_template_part('app/templates/loop'); ?>
        
        <?php } // End loop types ?>
    
    <?php } else { // If no posts ?>
    
        <?php get_template_part('app/templates/no_posts'); ?>
    
    <?php } // End loop ?>
    
    <?php comments_template(); ?>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
