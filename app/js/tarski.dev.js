/**
 *  Tarski
 *
 *  The global namespace for all of Tarski's JavaScript.
 **/
window.Tarski = {};

/**
 *  new Tarski.Navbar(container)
 *  - container (HTMLElement|String|jQuery): the navigation container
 *
 *  [[Tarski.Navbar]] is a small wrapper around the normal CSS dropdowns to
 *  ensure compatibility with older browsers such as Internet Explorer 6 which
 *  do not support the :hover pseduo-class on elements that are not anchors,
 *  and do a more intelligent job of positioning dropdowns so that they remain
 *  within the theme layout.
 **/
Tarski.Navbar = function(container) {
    var navbar      = this;
    this._container = jQuery(container).first();
    this._parent    = this._container.parent();
    this._items     = this._container.children('li');
    
    this._items.each(function(i, el) {
        jQuery(el).hover(function() {
            navbar.expand(this);
        }, function() {
            navbar.collapse(this);
        });
        
        navbar.collapse(el);
    });
};

/**
 *  Tarski.Navbar#expand(element)
 *  - element (HTMLElement|String|jQuery): the top-level menu item whose
 *    associated submenu is to be expanded.
 **/
Tarski.Navbar.prototype.expand = function(element) {
        element = jQuery(element);
    var submenu = element.children('.sub-menu');
    
    if (submenu.length < 1) return;
    
    submenu.removeClass('collapsed').addClass('expanded').show();
    
    this._reposition(submenu);
};

/**
 *  Tarski.Navbar#collapse(element)
 *  - element (HTMLElement|String|jQuery): the top-level menu item whose
 *    associated submenu is to be collapsed.
 **/
Tarski.Navbar.prototype.collapse = function(element) {
        element = jQuery(element);
    var submenu = element.children('.sub-menu');
    
    if (submenu.length < 1) return;
    
    submenu.hide().removeClass('expanded').addClass('collapsed');
};

Tarski.Navbar.prototype._reposition = function(submenu) {
    var wrapperOffset = this._parent.offset().left,
        wrapperWidth  = this._parent.width(),
        menuOffset    = submenu.offset().left,
        menuWidth     = submenu.outerWidth(),
        leftDiff      = menuOffset - wrapperOffset,
        rightDiff     = menuOffset + menuWidth - wrapperOffset - wrapperWidth,
        parent, rightShift;
    
    if (leftDiff <= 0) {
        submenu.css({
            left: 0,
            right: 'auto'
        });
    } else if (rightDiff >= 0) {
        parent     = submenu.parent();
        rightShift = parent.offset().left
                   + parent.width()
                   - wrapperOffset
                   - wrapperWidth;
        
        submenu.css({
            left: 'auto',
            right: rightShift + 'px'
        });
    }
};

/**
 *  new Tarski.Searchbox(field, label)
 *  - field (HTMLElement): the search field
 *  - label (HTMLElement): the label for the search field
 *
 *  This type provides a fallback for search forms in browsers where the
 *  HTML5 placeholder attribute is not supported. When creating instances of
 *  [[Tarski.Searchbox]], pass in the input element and the label for that
 *  element as the arguments to the constructor.
 **/
Tarski.Searchbox = function(field, label) {
    var self = this, text;
    
    this._field = jQuery(field);
    this._label = jQuery(label).hide();
    
    if (this.constructor.PLACEHOLDER_SUPPORTED) return;
    
    if (text = this._field.attr('placeholder')) {
        this._text = text;
    } else {
        this._text = this._label.text();
    }
    
    this._field.focus(function() { self.focus(); });
    this._field.blur(function() { self.blur(); });
    
    this.blur();
};

/**
 *  Tarski.Searchbox#focus() -> Tarski.Searchbox
 *
 *  Removes any text in the text field, unless the user has entered a search
 *  query.
 **/
Tarski.Searchbox.prototype.focus = function() {
    if (this._field.val() === this._text) {
        this._field.val('');
    }
    
    return this;
};

/**
 *  Tarski.Searchbox#blur() -> Tarski.Searchbox
 *
 *  Resets the text field content to the default text, unless the user has
 *  entered a search query.
 **/
Tarski.Searchbox.prototype.blur = function() {
    var current = this._field.val();
    
    if (current === '') {
        this._field.val(this._text);
    }
    
    return this;
};

/**
 *  Tarski.Searchbox.PLACEHOLDER_SUPPORTED -> Boolean
 *
 *  Lets us know whether the HTML5 placeholder attribute for text input fields
 *  is supported or not.
 **/
Tarski.Searchbox.PLACEHOLDER_SUPPORTED = (function() {
    var input = document.createElement('input');
    return 'placeholder' in input;
})();
