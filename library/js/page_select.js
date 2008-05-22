function CollapsibleList(container) {
	
	this.container = jQuery(container);
	this.main = jQuery(container.children('p')[0]);
	this.list = jQuery(container.children('ol')[0]);
	
	this.toggle = function() {
		this.list.slideToggle(150);
		
		if ( this.container.hasClass('collapsed') )
			this.expand();
		else
			this.collapse();
	}
	
	this.collapse = function() {
		this.container.addClass('collapsed');
	}
	
	this.expand = function() {
		this.container.removeClass('collapsed');
	}
	
	this.addToggle = function(toggler) {
		scopeFix = this;
		this.toggler = jQuery(toggler);
		this.main.prepend(toggler);
		this.toggler.bind('click', function(ev) {
			scopeFix.toggle();
		});
	}
	
}

jQuery(document).ready(function() {
	jQuery('#navbar-select').addClass('js');
	
	var toggler = jQuery('<span class="toggle">Toggle</span>');
	
	jQuery('#navbar-select ol').each(function(i) {
		list = new CollapsibleList(jQuery(this).parent());
		list.collapse();
		list.addToggle(toggler);
		console.log(list.toggler);
	});
});
