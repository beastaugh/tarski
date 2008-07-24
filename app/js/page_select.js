// Included for cross-browser compatibility
if (!Array.prototype.reduce)
{
  Array.prototype.reduce = function(fun /*, initial*/)
  {
    var len = this.length;
    if (typeof fun != "function")
      throw new TypeError();

    // no value to return if no initial value and an empty array
    if (len == 0 && arguments.length == 1)
      throw new TypeError();

    var i = 0;
    if (arguments.length >= 2)
    {
      var rv = arguments[1];
    }
    else
    {
      do
      {
        if (i in this)
        {
          rv = this[i++];
          break;
        }

        // if array contains no values, no initial value to return
        if (++i >= len)
          throw new TypeError();
      }
      while (true);
    }

    for (; i < len; i++)
    {
      if (i in this)
        rv = fun.call(null, rv, this[i], i, this);
    }

    return rv;
  };
};

var UniqueNumList = function(memo, item) {
  if (Number(item) > 0 && jQuery.inArray(item, memo) < 0) memo.push(item);
  return memo;
};

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
		this.toggler.addClass('collapsed-toggle');
		this.container.addClass('collapsed');
		collapsed_page_set.addPage(this.root_id);
	};
	
	this.expand = function() {
		this.list.slideDown(150);
    this.toggler.removeClass('collapsed-toggle');
		this.container.removeClass('collapsed');
		collapsed_page_set.removePage(this.root_id);
	};
	
	this.addToggle = function(toggler) {
		var thisp = this;
		this.toggler = jQuery(toggler);
		
		if (this.container.hasClass('collapsed')) this.toggler.addClass('collapsed-toggle');
		
		this.main.prepend(this.toggler);
		this.toggler.bind('click', function(ev) {
			thisp.toggle();
		});
	};
	
};

function CollapsedPageSet(selector) {
	this.pages = [];
	
	this.retrieve = function() {
		this.pages = jQuery(selector).val().split(',').reduce(UniqueNumList, []);
	};
	
	this.save = function() {
		this.pages = this.pages.reduce(UniqueNumList, []);
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
