<p><?php _e('The sidebar for posts and pages can be the same as that for index pages, or use its own set of widgets.','tarski'); ?></p>
    
<label for="sidebar-pp-type">
    <input type="hidden" name="sidebar_pp_type" value="0" />
    <input type="checkbox" id="sidebar-pp-type" name="sidebar_pp_type" value="main" <?php if(get_tarski_option('sidebar_pp_type') == 'main') { echo 'checked="checked" '; } ?>/>
    <?php _e('Same content as main sidebar?','tarski'); ?>
</label>
