// tarski.js - external JS for Tarski

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
		if (sBox.value == 'Search this site')
			{
			sBox.value = '';
			}
	  	},
	blur : function()
		{
		var sBox = document.getElementById('s');
		if (sBox.value == '' || sBox.value == ' ') {sBox.value = 'Search this site';}
		}
	};

// add event onload
addEventToObject(window,'onload',Searchbox.init);