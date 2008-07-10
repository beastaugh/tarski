function CollapsibleList(container, collapsed_page_set) {
	var id_format = /^page-list-(\d+)$/;
	
	this.container = jQuery(container);
	this.root_id = parseInt(this.container.attr('id').replace(id_format, '$1'));
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
		var thisp = this;
		this.toggler = jQuery(toggler);
		this.main.prepend(this.toggler);
		this.toggler.bind('click', function(ev) {
			thisp.toggle();
		});
	};
	
};

var UniqueNumList = function(item, memo) {
  return Number(item) > 0 && jQuery.inArray(item, memo) < 0;
};

function CollapsedPageSet(selector) {
	this.pages = [];
	
	this.retrieve = function() {
		this.pages = jQuery.grep(jQuery(selector).val().split(','), UniqueNumList);
	};
	
	this.save = function() {
	  this.pages = jQuery.grep(this.pages, UniqueNumList);
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
			this.pages = jQuery.grep(this.pages, function(item, memo) {
				return item != page_id;
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
