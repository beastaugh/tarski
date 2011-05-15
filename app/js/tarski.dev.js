/**
 *  Tarski
 *
 *  The global namespace for all of Tarski's JavaScript.
 **/
window.Tarski = {};

/**
 *  new Tarski.Navbar(navbar)
 *  - navbar (HTMLElement|String|jQuery): the navigation container
 *
 *  [[Tarski.Navbar]] is an expanding navigation component to display sub-menus
 *  within Tarski. It essentially provides an alternative to the normal
 *  dropdown-style menus.
 **/
Tarski.Navbar = function(navbar) {
    var self = this, lists;
    
    navbar = jQuery(navbar).addClass('expanded');
    lists  = navbar.children('ul.primary').children('.menu-item');
    
    this._container = navbar;
    this._listItems = jQuery.map(lists, function(el, i) {
        var item    = jQuery(el),
            submenu = item.children('.sub-menu');
        submenu.height(submenu.height());
        return item;
    });
    
    this._maxHeight     = this._container.height();
    this._maxMenuWidths = jQuery.map(this._listItems, function(el, i) {
        return el.width();
    });
    
    this._container.removeClass('expanded').addClass('collapsed');
    
    this._minHeight = this._container.height();
    this._minMenuWidths = jQuery.map(this._listItems, function(el, i) {
        var width = el.width();
        el.width(width);
        return width;
    });
    this._container.height(this._minHeight);
    
    this._toggle = jQuery('<span class="navbar-toggle">Expand</span>');
    this._toggle.click(function() {
        if (self.inState('COLLAPSED')) {
            self.expand();
        } else if (self.inState('EXPANDED')) {
            self.collapse();
        }
    });
    this._container.append(this._toggle);
    
    this.setState('COLLAPSED');
};

/**
 *  Tarski.Navbar#expand() -> Tarski.Navbar
 *
 *  Expands the navbar so that all sub-menus are displayed.
 **/
Tarski.Navbar.prototype.expand = function(cb) {
    var self = this;
    
    if (this.isAnimating()) return;
    
    this.setState('ANIMATING');
    
    this._container
        .removeClass('collapsed')
        .addClass('expanded')
        .animate(
            {height: this._maxHeight},
            Tarski.Navbar.EXPAND_TIME,
            function() {
                self._toggle.html('Collapse');
                self.setState('EXPANDED');
            });
    
    jQuery.each(this._listItems, function(i, el) {
        el.animate(
            {width: self._maxMenuWidths[i]},
            Tarski.Navbar.EXPAND_TIME);
    });
    
    return this;
};

/**
 *  Tarski.Navbar#expand() -> Tarski.Navbar
 *
 *  Collapses the navbar so that all sub-menus are hidden.
 **/
Tarski.Navbar.prototype.collapse = function(elem, cb) {
    var self = this;
    
    if (this.isAnimating()) return;
    
    this.setState('ANIMATING');
    
    this._container
        .animate(
            {height: this._minHeight},
            Tarski.Navbar.COLLAPSE_TIME,
            function() {
                self._container
                    .removeClass('expanded')
                    .addClass('collapsed');
                
                self._toggle.html('Expand');
                
                self.setState('COLLAPSED');
            });
    
    jQuery.each(this._listItems, function(i, el) {
        el.animate(
            {width: self._minMenuWidths[i]},
            Tarski.Navbar.COLLAPSE_TIME);
    });
    
    return this;
};

/**
 *  Tarski.Navbar#isAnimating() -> Boolean
 *
 *  Check whether the navbar is currently animating (i.e. is in the process of
 *  expanding or collapsing).
 **/
Tarski.Navbar.prototype.isAnimating = function() {
    return this.inState('ANIMATING');
};

/**
 *  Tarski.Navbar#inState(state) -> Boolean
 *  - state (string): the state being checked for
 *
 *  Check whether the navbar is currently in the state named by the given
 *  string.
 **/
Tarski.Navbar.prototype.inState = function(state) {
    return this._state === state;
};

/**
 *  Tarski.Navbar#setState(state) -> Boolean
 *  - state (string): the state to be set
 *
 *  Change the navbar's state to that named by the given string.
 **/
Tarski.Navbar.prototype.setState = function(state) {
    this._state = state;
};

/**
 *  Tarski.Navbar.EXPAND_TIME -> Number
 *
 *  The duration of the animation run when expanding the navbar.
 **/
Tarski.Navbar.EXPAND_TIME = 300;

/**
 *  Tarski.Navbar.COLLAPSE_TIME -> Number
 *
 *  The duration of the animation run when collapsing the navbar.
 **/
Tarski.Navbar.COLLAPSE_TIME = 300;

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

jQuery(document).ready(function() {
    var searchForm, searchField, searchLabel, searchBox, navbar;
    
    jQuery('body').addClass('js');
    
    navbar = new Tarski.Navbar(jQuery('#navigation'));
    
    searchField = jQuery('#s');
    searchLabel = jQuery('#searchlabel');
    
    if (searchField.length > 0 && searchLabel.length > 0) {
        searchBox = new Tarski.Searchbox(searchField, searchLabel);
    }
});
