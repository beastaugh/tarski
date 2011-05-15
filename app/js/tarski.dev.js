/**
 *  Tarski
 **/
window.Tarski = {};

Tarski.Navbar = function(navbar) {
    var self = this;
    
    this._container = jQuery(navbar).addClass('expanded');
    this._maxHeight = this._container.height();
    this._container.removeClass('expanded').addClass('collapsed');
    this._minHeight = this._container.height();
    this._container.height(this._minHeight)
    
    this._container.mouseenter(function() { self.expand(); });
    this._container.mouseleave(function() { self.collapse(); });
    
    this.setState('COLLAPSED');
};

Tarski.Navbar.prototype.expand = function(cb) {
    var self = this;
    
    if (this.isAnimating()) return;
    
    this.setState('ANIMATING');
    
    this._container
        .removeClass('collapsed')
        .addClass('expanded')
        .animate(
            {height: this._maxHeight},
            500,
            function() { self.setState('EXPANDED'); });
    
    return this;
};

Tarski.Navbar.prototype.collapse = function(elem, cb) {
    var self = this;
    
    if (this.isAnimating()) return;
    
    this.setState('ANIMATING');
    
    this._container
        .animate(
            {height: this._minHeight},
            500,
            function() {
                self._container
                    .removeClass('expanded')
                    .addClass('collapsed');
                
                self.setState('COLLAPSED');
            });
    
    return this;
};

Tarski.Navbar.prototype.setNextAction = function(action) {
    this._nextAction = action;
};

Tarski.Navbar.prototype.fireNextAction = function() {
    if (Tarski.Navbar.ACTIONS.indexOf(this._nextAction) === 1) {
        this[this._nextAction](function(self) {
            self._nextAction = null;
        });
    }
};

Tarski.Navbar.prototype.isAnimating = function() {
    return this.inState('ANIMATING');
};

Tarski.Navbar.prototype.inState = function(state) {
    return this._state === state;
};

Tarski.Navbar.prototype.setState = function(state) {
    this._state = state;
};

Tarski.Navbar.ACTIONS = ['expand', 'collapse'];

/**
 *  new Tarski.Searchbox(field, label)
 *  - field (HTMLElement): the search field
 *  - label (HTMLElement): the label for the search field
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
