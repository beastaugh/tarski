// Object event handler
function addEventToObject(obj, evt, func) {
	var oldhandler = obj[evt];
	obj[evt] = (typeof obj[evt] != 'function') ? func : function(){oldhandler();func();};
}

// Replace element's empty value attribute with another element's content
function replaceEmpty(el1, el2) {
	if (/^\s*$/.test(el1.value)) {
		el1.value = el2.firstChild.nodeValue;
	}
}

// Search box stuff
var Searchbox = {
	init : function() {
		this.sBox = document.getElementById('s');
		this.sLabel = document.getElementById('searchlabel');
		if (this.sBox && this.sLabel) {
			this.sLabel.style.display = 'none';
			replaceEmpty(this.sBox, this.sLabel);
			addEventToObject(this.sBox, 'onclick', Searchbox.click);
			addEventToObject(this.sBox, 'onfocus', Searchbox.click);
			addEventToObject(this.sBox, 'onblur', Searchbox.blur);
		}
	},
	click : function() {
		if (sBox.value == sLabel.firstChild.nodeValue) {
			sBox.value = '';
		}
	},
	blur : function() {
		replaceEmpty(sBox, sLabel);
	}
};

// Add event onload
addEventToObject(window, 'onload', Searchbox.init);