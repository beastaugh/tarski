Function.prototype.bind = function(object) {
	var method = this;
	return function() {
		return method.apply(object, arguments);
	};
};

/**
 * <p>Returns a function that returns the result of applying the function to its arguments,
 * but that logs its input and output to the Firebug console. Derived from a similar function
 * in Oliver Steele's Functional library.</p>
 *
 * Copyright: Copyright 2007 by Oliver Steele.  All rights reserved.
 * http://osteele.com/sources/javascript/functional/
 *
 * @param {String} name The function name used when messages are logged to the console
 * @param {String} func The console function to use. Defaults to 'info'
 * @returns {Function}
 */
Function.prototype.traced = function(name, func) {
	var method = this, name = name || this, func = func || 'info';
	return function() {
		window.console && console[func](name, ' called on ', this, ' with ', arguments);
		var result = method.apply(this, arguments);
		window.console && console[func](name, ' -> ', result);
		return result;
	};
};

function addEvent( obj, type, fn ) {
	if (obj.addEventListener) {
		obj.addEventListener( type, fn, false );
		EventCache.add(obj, type, fn);
	}
	else if (obj.attachEvent) {
		obj["e"+type+fn] = fn;
		obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
		obj.attachEvent( "on"+type, obj[type+fn] );
		EventCache.add(obj, type, fn);
	}
	else {
		obj["on"+type] = obj["e"+type+fn];
	}
}

var EventCache = function(){
	var listEvents = [];
	return {
		listEvents : listEvents,
		add : function(node, sEventName, fHandler){
			listEvents.push(arguments);
		},
		flush : function(){
			var i, item;
			for(i = listEvents.length - 1; i >= 0; i = i - 1){
				item = listEvents[i];
				if(item[0].removeEventListener){
					item[0].removeEventListener(item[1], item[2], item[3]);
				};
				if(item[1].substring(0, 2) != "on"){
					item[1] = "on" + item[1];
				};
				if(item[0].detachEvent){
					item[0].detachEvent(item[1], item[2]);
				};
				item[0][item[1]] = null;
			};
		}
	};
}();
addEvent(window,'unload',EventCache.flush);

/**
 * <p>Replaces element el1's empty 'value' attribute with element el2's content.</p>
 * @param {Object} replaceable
 * @param {Object} replacing
 */
function replaceEmpty(replaceable, replacing) {
	if (/^\s*$/.test(replaceable.value)) {
		replaceable.value = replacing.firstChild.nodeValue;
	}
}

/**
 * <p>Search box object, allowing us to add some default text to the search
 * field which will then be removed when that field is given focus. It remains
 * accessible because the default text is pulled from the search field's label
 * and that label is only hidden when JavaScript is enabled.</p>
 */
var Searchbox = {
	
	/**
	 * <p>If the search box and associated label exist, hide the label and
	 * add the label's content to the search box. Then add two events to the
	 * search box, one which will reset the box's content when it's given focus
	 * and one which will add the label content back when it loses focus (as
	 * long as the box is empty).</p>
	 */
	init : function() {
		this.sBox = document.getElementById('s');
		this.sLabel = document.getElementById('searchlabel');
		if (this.sBox && this.sLabel) {
			this.sLabel.style.display = 'none';
			replaceEmpty(this.sBox, this.sLabel);
			addEvent(this.sBox, 'focus', this.reset_text.bind(this));
			addEvent(this.sBox, 'blur', this.add_text.bind(this));
		}
	},
	
	/**
	 * <p>Removes the search box's default content.</p>
	 */
	reset_text : function() {
		if (this.sBox.value == this.sLabel.firstChild.nodeValue) {
			this.sBox.value = '';
		}
	},
	
	/**
	 * <p>Adds the search box's default content back in if it's empty.</p>
	 */
	add_text : function() {
		replaceEmpty(this.sBox, this.sLabel);
	}
};

addEvent(window, 'load', Searchbox.init.bind(Searchbox));