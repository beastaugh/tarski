<?php

define('WP_USE_THEMES', false);
require('../../../../wp-blog-header.php');
header('Content-type: text/javascript');

?>// tarski.js - external JS for Tarski

// Default search box text
//-----------------------------------

// event handler
function addEventToObject(obj,evt,func) {
	var oldhandler = obj[evt];
	obj[evt] = (typeof obj[evt] != 'function') ? func : function(){oldhandler();func();};
}

// search box stuff
var Searchbox = {
	init : function()
		{
		var sBox = document.getElementById('s');
		if (sBox)
			{
			addEventToObject(sBox,'onclick',Searchbox.click);
			addEventToObject(sBox,'onblur',Searchbox.blur);
			}	
		},
	click : function()
		{
		var sBox = document.getElementById('s');
		if (sBox.value == '<?php _e('Search this site','tarski'); ?>')
			{
			sBox.value = '';
			}
	  	},
	blur : function()
		{
		var sBox = document.getElementById('s');
		if (sBox.value == '' || sBox.value == ' ') {sBox.value = '<?php _e('Search this site','tarski'); ?>';}
		}
	};

// add event onload
addEventToObject(window,'onload',Searchbox.init);