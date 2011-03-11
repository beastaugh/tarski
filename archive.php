<?php get_header(); ?>

<div class="primary posts">
    <?php if (have_posts()) { // Gets it all going ?>
        <div class="archive">
            <?php if (is_category()) { // Category header ?>
                <div class="meta">
                    <h1><?php single_cat_title(); ?></h1>
                </div>
                
                <div class="content">
                    <?php if (trim(strip_tags(category_description()))) { echo category_description(); } else { ?>
                        <p><?php printf(__('You are currently browsing the archive for the %s category.','tarski'), '<strong>' . single_cat_title('', false) . '</strong>'); ?></p>
                    <?php } ?>
                </div>
            <?php } elseif (is_tag()) { // Tag archive header ?>
                <div class="meta">
                    <h1 class="title"><?php echo multiple_tag_titles(); ?></h1>
                </div>
                
                <div class="content">
                    <p><?php printf(__('You are currently browsing articles tagged %s.', 'tarski'), multiple_tag_titles('<strong>%s</strong>')); ?></p>
                </div>
            <?php } elseif (is_author()) { // Author header ?>
                <div class="meta">
                    <h1 class="title"><?php printf(__('Articles by %s','tarski'), the_archive_author_displayname()); ?></h1>
                </div>
                
                <div class="content">
                    <?php if(the_archive_author_description()) { ?>
                        <?php echo wpautop(wptexturize(stripslashes(the_archive_author_description()))); ?>
                    <?php } else { ?>
                        <p><?php printf(__('You are currently browsing %s&#8217;s articles.', 'tarski'), '<strong>' . the_archive_author_displayname() . '</strong>'); ?></p>
                    <?php } ?>
                </div>
            <?php } elseif (is_day()) { // Daily archive header ?>
                <div class="meta">
                    <h1 class="title"><?php the_time(get_option('date_format')); ?></h1>
                </div>
                
                <div class="content">
                    <p><?php printf(__('You are currently browsing the daily archive for %s.', 'tarski'), '<strong>' . get_the_time(get_option('date_format')) . '</strong>'); ?></p>
                </div>
            <?php } elseif (is_month()) { // Monthly archive header ?>
                <div class="meta">
                    <h1 class="title"><?php the_time(__('F Y', 'tarski')); ?></h1>
                </div>
                
                <div class="content">
                    <p><?php printf(__('You are currently browsing the monthly archive for %s.', 'tarski'), '<strong>' . get_the_time(__('F Y', 'tarski')) . '</strong>'); ?></p>
                </div>
            <?php } elseif (is_year()) { // Yearly archive header ?>
                <div class="meta">
                    <h1 class="title"><?php the_time(__('Y', 'tarski')); ?></h1>
                </div>
                
                <div class="content">
                    <p><?php printf(__('You are currently browsing the yearly archive for %s.','tarski'), '<strong>' . get_the_time(__('Y', 'tarski')) . '</strong>'); ?></p>
                </div>
            <?php } ?>
        </div>
        
        <?php get_template_part('app/templates/loop'); ?>
        
    <?php } else { get_template_part('app/templates/no_posts'); } // End if posts ?>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
