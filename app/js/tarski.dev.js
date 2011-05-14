/**
 *  Tarski
 **/
window.Tarski = {};

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
    var searchForm, searchField, searchLabel, searchBox;
    
    jQuery('body').addClass('js');
    
    searchField = jQuery('#s');
    searchLabel = jQuery('#searchlabel');
    
    if (searchField.length > 0 && searchLabel.length > 0) {
        searchBox = new Tarski.Searchbox(searchField, searchLabel);
    }
});
