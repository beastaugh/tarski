var Radios = function(selector) {
  this._elements = [];
  var radios = this;
  jQuery(selector).each(function(i) {
    radios._elements.push(new Radio(radios, this));
  });
};

Radios.prototype.check = function(radio) {
  var i = this._elements.length;
  while (i--) {
    this._elements[i]._input.checked = false;
    jQuery(this._elements[i]._label).removeClass('checked');
  }
  radio._input.checked = true;
  jQuery(radio._label).addClass('checked');
};

var Radio = function(group, element) {
  this._setup(group, element);
  var radio = this;
  
  jQuery([this._input, this._label]).click(function(event) {
    radio.group.check(radio);
  });
  
  jQuery(this._label).mouseover(function(event) {
    jQuery(this).addClass('hovered');
  });
  
  jQuery(this._label).mouseout(function(event) {
    jQuery(this).removeClass('hovered');
  });
};

Radio.prototype._setup = function(group, input) {
  this.group = group;
  this._input = input;
  this._label = document.getElementById('for_' + this._input.id);
  
  jQuery(this._input).wrap('<span style="position:relative;"></span>');
  jQuery(this._input).css({position: 'absolute', left: '-9999em'});
  
  if (this._input.checked) jQuery(this._label).addClass('checked');
}

jQuery(document).ready(function() {
  new Radios('#tarski-headers input');
});
