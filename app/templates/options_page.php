<div id="tarski-options" class="wrap metabox-holder <?php if(get_bloginfo("text_direction") == "rtl") { echo " rtl"; } ?>">
    
    <?php tarski_options_fragment('restore_options'); ?>
    
    <form action="<?php echo admin_url('admin-post.php?action=tarski_options'); ?>" method="post">
        
        <?php wp_nonce_field('admin_post_tarski_options', '_wpnonce_tarski_options'); ?>
        
        <div id="tarski-options-header">
            <h2><?php _e('Tarski Options', 'tarski'); ?></h2>
            
            <p id="tarski-save-options">
                <input type="submit" class="button-primary" name="submit" value="<?php _e('Save Options','tarski'); ?>" />
            </p>
            
            <p id="tarski-info">
                <a href="http://tarskitheme.com/help/"><?php _e('Tarski documentation','tarski'); ?></a>
                | <a href="http://tarskitheme.com/credits/"><?php _e('Credits &amp; Thanks','tarski'); ?></a>
            </p>
            
            <div class="clearer"></div>
        </div>
        
        <?php tarski_options_fragment('messages'); ?>
        
        <div class="primary">
            <?php tarski_options_block('alternate_style', __('Alternate Style', 'tarski')); ?>
            <?php tarski_options_fn_block('tarski_miscellaneous_options', __('Miscellaneous Options', 'tarski')); ?>
            <p><input type="submit" class="button-primary" name="submit" value="<?php _e('Save Options','tarski'); ?>" /></p>
        </div>
        
        <div class="secondary">
            <?php tarski_options_block('sidebar_options', __('Sidebar Options', 'tarski')); ?>
        </div>
    </form>
    
    <?php if (get_option('tarski_options') && !is_numeric(get_raw_tarski_option('deleted'))) { ?>
        <div class="secondary">
            <?php tarski_options_block('reset_options', __('Reset Options', 'tarski')); ?>
        </div>
    <?php } ?>
    
    <div class="clearer"></div>
</div>
