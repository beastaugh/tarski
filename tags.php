<?php
/*
Template Name: Tags
*/
@include(TEMPLATEPATH . '/constants.php');
get_header(); ?>
	<div class="primary archive">
	<?php if (have_posts()) { while (have_posts()) { the_post(); ?>
		<div class="meta">
			<h1 class="title"><?php the_title(); ?></h1>
		</div>

<?php if(function_exists("UTW_ShowWeightedTagSetAlphabetical")) {
	echo "<h3>Tag Cloud</h3>\n";
	echo "<div class=\"tagcloud\">\n";
	UTW_ShowWeightedTagSetAlphabetical("coloredsizedtagcloud");
	echo "</div>\n";
	
	if(get_option('tarski_ajax_tags')) {
?>

<script language="javascript">
var ajaxUrl = "<?php echo get_settings('home'); ?>/wp-content/plugins/UltimateTagWarrior/ultimate-tag-warrior-ajax.php";

function createRequestObject() {
    var ro;
    var browser = navigator.appName;
    if(browser == "Microsoft Internet Explorer"){
        ro = new ActiveXObject("Microsoft.XMLHTTP");
    }else{
        ro = new XMLHttpRequest();
    }
    return ro;
}

var searchTags = new Array();

function searchFor(item,tagid,related) {
	http = createRequestObject();

	if (tagid != '' && indexOf(searchTags, item) != -1) {
		searchTags.splice(indexOf(searchTags, item), 1);
		document.getElementById('tag_' + tagid).style.fontWeight="normal";
		document.getElementById('tag_' + tagid).style.border="none";

		related += ',' + tagid;

		relatedids = related.split(',');
		for (i = 0; i < relatedids.length; i++) {
			if (relatedids[i] != "") {
				currentsize = document.getElementById('tag_' + relatedids[i]).style.fontSize;

				if (currentsize == "") {
					document.getElementById('tag_' + relatedids[i]).style.fontSize = "12px";
				} else {
					currentint = currentsize.substring(0, currentsize.length - 2) * 1;
					currentint-= 8;
					document.getElementById('tag_' + relatedids[i]).style.fontSize = currentint + "px";
				}
			}
		}

	} else if (tagid != '') {
		searchTags[searchTags.length] = item;
		document.getElementById('tag_' + tagid).style.fontWeight="bolder";
		document.getElementById('tag_' + tagid).style.border="1px solid #ccc";

		related += ',' + tagid;
		relatedids = related.split(',');
		for (i = 0; i < relatedids.length; i++) {
			if (relatedids[i] != "") {
				currentsize = document.getElementById('tag_' + relatedids[i]).style.fontSize;

				if (currentsize == "") {
					document.getElementById('tag_' + relatedids[i]).style.fontSize = "18px";
				} else {
					currentint = currentsize.substring(0, currentsize.length - 2) * 1;
					currentint+= 8;
					document.getElementById('tag_' + relatedids[i]).style.fontSize = currentint + "px";
				}
			}
		}
	}

	searchtype = "any";
	for(i = 0; i < document.forms['searchselector'].elements['searchtype'].length; i++) {

		if (document.forms['searchselector'].elements['searchtype'][i].checked) {
			searchtype = document.forms['searchselector'].elements['searchtype'][i].value;
		}
	}

    http.open('get', ajaxUrl+'?action=tagSearch&tag='+serialiseTags()+'&type='+searchtype);
    http.onreadystatechange = handleSearchResponse;
    http.send(null);
}

function handleSearchResponse() {
    if(http.readyState == 4){
        var response = http.responseText;
        var update = new Array();

        document.getElementById("matches").innerHTML = response;
    }
}

function serialiseTags() {
	var tags = "";
	for (i = 0; i < searchTags.length; i++) {
		tags += searchTags[i] + "|";
	}
	return tags;
}

function indexOf(array, item) {
	for (i = 0; i < array.length; i++) {
		if (array[i] == item) {
			return i;
		}
	}

	return -1;
}

</script>

<h3><?php _e('Search Tags', 'tarski'); ?></h3>
<form name="searchselector" id="searchselector">
<input type="radio" name="searchtype" value="all" id="all" onchange="searchFor('','','')"/> <label for="all"><?php _e('All of the selected tags', 'tarski'); ?></label>
<input type="radio" name="searchtype" value="any" id="any"  onchange="searchFor('','','')" checked="checked" /> <label for="any"><?php _e('Any of the selected tags', 'tarski'); ?></label><br />
</form>

<div class="tagcloud">
<?php UTW_ShowWeightedTagSetAlphabetical("", array('default'=>'<a id="tag_%tagid%" href="javascript:searchFor(\'%tag%\', \'%tagid%\', \'%relatedtagids%\')" style=\'font-size:12px; border:none\'>%tagdisplay%</a> &middot; '), 0) ?>
</div>

<div id="matches"></div>

<?php } // end ajax tag search if
} else { ?>
	<div class="content">
		<p><?php _e('Ultimate Tag Warrior is not active.', 'tarski'); ?></p>
	</div>
<?php } } } else { ?>
		<div class="meta">
			<h1 class="title" id="post-<?php the_ID(); ?>"><?php the_title(); ?></h1>
		</div>
		<div class="content">
			<p><?php _e('Nothing here, sorry!', 'tarski'); ?></p>
		</div>
<?php } echo $pageEndInclude; ?>
</div> <!-- /primary -->
<?php get_sidebar();
get_footer(); ?>