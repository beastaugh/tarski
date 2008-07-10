// Array.unique() - Remove duplicate values from an array
Array.prototype.unique = function() {		
	var a = [], i, l = this.length;

	for (i=0; i<l; i++) {
		if (a.indexOf(this[i], 0) < 0) {
			a.push(this[i]);
		}
	}

	return a;
};

// Array.stripZeroes() - Remove values of 0 and '0' from an array
Array.prototype.stripZeroes = function() {
	var a = [], i, l = this.length;
	
	for (i=0; i<l; i++) {
		if (parseInt(this[i]) != 0) {
			a.push(this[i]);
		}
	}
	
	return a;
};

function CollapsibleList(container, collapsed_page_set) {
	
	this.container = jQuery(container);
	this.root_id = parseInt(this.container.attr('id').replace(/^page-list-(\d+)$/, ('$1')));
	this.main = jQuery(container.children('p')[0]);
	this.list = jQuery(container.children('ol')[0]);
	
	this.toggle = function() {
		if ( this.container.hasClass('collapsed') ) {
			this.expand();
		} else {
			this.collapse();
		}
	};
	
	this.collapse = function() {
		this.list.slideUp(150);
		this.container.addClass('collapsed');
		collapsed_page_set.addPage(this.root_id);
	};
	
	this.expand = function() {
		this.list.slideDown(150);
		this.container.removeClass('collapsed');
		collapsed_page_set.removePage(this.root_id);
	};
	
	this.addToggle = function(toggler) {
		var scopeFix = this;
		this.toggler = jQuery(toggler);
		this.main.prepend(this.toggler);
		this.toggler.bind('click', function(ev) {
			scopeFix.toggle();
		});
	};
	
};

function CollapsedPageSet(selector) {
	
	this.pages = [];
	
	this.retrieve = function() {
		var field = jQuery(selector).val();
		this.pages = field.split(',').map(Number).unique().stripZeroes();
	};
	
	this.save = function() {
		this.pages = this.pages.unique().stripZeroes();
		jQuery(selector).val(this.pages.join(','));
	};
	
	this.addPage = function(page_id) {
		this.retrieve();
		
		if (this.pages instanceof Array) {
			this.pages.push(page_id);
		} else {
			this.pages = [page_id];
		}
		
		this.save();
	};
	
	this.removePage = function(page_id) {
		this.retrieve();
		
		if (this.pages instanceof Array) {
			this.pages = jQuery.grep(this.pages, function(n) {
				return n != page_id;
			});
		} else {
			this.pages = [];
		}
		
		this.save();
	};
	
};

jQuery(document).ready(function() {
	jQuery('#navbar-select').addClass('js');
	
	var collapsed_page_set = new CollapsedPageSet('#opt-collapsed-pages');
	
	jQuery('#navbar-select ol').each(function(i) {
		list = new CollapsibleList(jQuery(this).parent(), collapsed_page_set);
		list.addToggle('<span class="toggle">Toggle</span>');
	});
});
