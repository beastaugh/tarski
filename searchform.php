<?php $labelText = __('Search this site', 'tarski'); ?>
<div class="searchbox">
    <form method="get" id="searchform" action="<?php echo home_url(); ?>"><fieldset>
        <label for="s" id="searchlabel"><?php echo $labelText ?></label>
        <input type="search" placeholder="<?php echo $labelText ?>" value="<?php the_search_query(); ?>" name="s" id="s">
        <input type="submit" id="searchsubmit" value="<?php _e('Search','tarski'); ?>">
    </fieldset></form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var searchField = document.getElementById('s'),
            searchLabel = document.getElementById('searchlabel'),
            searchBox;
        
        if (searchField && searchLabel) {
            searchBox = new Tarski.Searchbox(searchField, searchLabel);
        }
    });
</script>
