<?php
/**
 * @package WordPress
 * @subpackage Tarski
 */

/**
 * Output the Tarski sidebar.
 *
 * @since 2.0
 *
 * @uses is_page_template
 * @uses dynamic_sidebar
 *
 * @return void
 */
function tarski_sidebar() {
    if (is_page_template('archives.php')) return;
    
    $sidebar = is_singular() && (get_tarski_option('sidebar_pp_type') != 'main')
             ? 'sidebar-post-and-page'
             : 'sidebar-main';
    
    dynamic_sidebar($sidebar);
}

/**
 * Output footer main widgets field.
 *
 * @since 2.1
 *
 * @uses dynamic_sidebar
 *
 * @return mixed
 */
function tarski_footer_main() {
    dynamic_sidebar('footer-main');
}

/**
 * Output the footer sidebar widgets field.
 *
 * @since 2.0
 *
 * @uses dynamic_sidebar
 * @return mixed
 */
function tarski_footer_sidebar() {
    dynamic_sidebar('footer-sidebar');
}

/**
 * Wrap text widgets in content div with edit link.
 *
 * @since 2.1
 *
 * @param string $text
 * @return string
 */
function tarski_widget_text_wrapper($text) {
    return strlen(trim($text)) > 0
        ? "<div class=\"content\">\n\n$text</div>\n"
        : '';
}

/**
 * Remove navbar links from the links widget.
 *
 * @since 2.2
 *
 * @uses get_tarski_option
 *
 * @param array $args
 * @return array
 */
function tarski_widget_links_args($args) {
    $args = is_array($args) ? $args : array();
    $args['exclude_category'] = get_tarski_option('nav_extlinkcat');
    return $args;
}

/**
 * class Tarski_Widget_Recent_Entries
 *
 * Recent entries á la Tarski.
 *
 * Lists the five most recent entries, or, on the home page, the five most
 * recent entries after those posts actually displayed on the page.
 *
 * @package Tarski
 *
 * @since 2.5
 */
class Tarski_Widget_Recent_Entries extends WP_Widget {
    
    function Tarski_Widget_Recent_Entries() {
        $widget_ops = array('classname' => 'recent-articles', 'description' => __('The most recent articles, offset by the number of visible articles on the home page.', 'tarski'));
        $this->WP_Widget('recent-articles', __('Recent Articles', 'tarski'), $widget_ops);
        $this->alt_option_name = 'tarski_recent_entries';
        
        add_action('save_post', array($this, 'flush_widget_cache'));
        add_action('deleted_post', array($this, 'flush_widget_cache'));
        add_action('switch_theme', array($this, 'flush_widget_cache'));
    }
    
    function widget($args, $instance) {
        global $posts;
        
        $cache = wp_cache_get('tarski_recent_entries', 'widget');
        
        if (!is_array($cache))
            $cache = array();
        
        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }
        
        ob_start();
        extract($args);
        
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Articles', 'tarski') : $instance['title']);
        if (!$number = (int) $instance['number'])
            $number = 10;
        else if ($number < 1)
            $number = 1;
        else if ($number > 15)
            $number = 15;
        
        $r = new WP_Query(array(
            'showposts' => $number,
            'nopaging' => 0,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
            'offset' => is_home() ? count($posts) : 0));
        if ($r->have_posts()) :
?>

<?php echo $before_widget; ?>
    <?php if ($title) echo $before_title . $title . $after_title; ?>
    <ul>
        <?php while ($r->have_posts()) : $r->the_post(); ?>
        <li>
            <h4 class="recent-title"><a title="<?php _e('View this post', 'tarski'); ?>" href="<?php the_permalink(); ?>"><?php the_title() ?></a></h4>
            <p class="recent-metadata"><?php printf(get_tarski_option('show_categories') ? __('%1$s in %2$s', 'tarski') : '%s',
                the_time(get_option('date_format')),
                get_the_category_list(', ', '', false)); ?></p>
            <div class="recent-excerpt content"><?php the_excerpt(); ?></div>
        </li>
        <?php endwhile; ?>
    </ul>
<?php echo $after_widget; ?>
<?php
            wp_reset_query();  // Restore global post data stomped by the_post().
        endif;
        
        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_add('tarski_recent_entries', $cache, 'widget');
    }
    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $this->flush_widget_cache();
        
        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['tarski_recent_entries']))
            delete_option('tarski_recent_entries');
        
        return $instance;
    }
    
    function flush_widget_cache() {
        wp_cache_delete('tarski_recent_entries', 'widget');
    }
    
    function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        if (!isset($instance['number']) || !$number = (int) $instance['number'])
            $number = 5;
?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'tarski'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to display:', 'tarski'); ?></label>
        <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /><br />
        <small><?php _e('(at most 15)', 'tarski'); ?></small></p>
<?php
    }
}

?>