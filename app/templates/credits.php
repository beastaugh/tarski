<div class="primary content">
    <p><?php _e('Powered by <a href="http://wordpress.org/">WordPress</a> and <a href="http://tarskitheme.com/">Tarski</a>', 'tarski');
    if (is_multisite()) {
        $site = get_current_site();
        
        printf(__(' | Hosted by %s', 'tarski'),
            '<a href="http://' .
            $site->domain .
            $site->path .
            '">' . $site->site_name . '</a>');
    } ?></p>
</div>
