<div class="primary content">
    <p><?php _e('Powered by <a href="http://wordpress.org/">WordPress</a> and <a href="http://tarskitheme.com/">Tarski</a>', 'tarski');
    if (is_multisite()) {
        printf(__(' | Hosted by %s', 'tarski'),
            '<a href="http://' .
            get_current_site()->domain .
            get_current_site()->path .
            '">' . get_current_site_name() . '</a>');
    } ?></p>
</div>
