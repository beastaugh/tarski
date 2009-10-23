var Radios = function(selector) {
  this._elements = [];
  var radios     = this;
  jQuery(selector).each(function(i) {
    radios.add(new Radio(radios, this));
  });
};

Radios.prototype.add = function(item) {
  if (item._input && item._label) this._elements.push(item);
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

var Radio = function(group, input) {
  this.group  = group;
  this._input = input;
  this._label = document.getElementById('for_' + this._input.id);
  this._setup(this._group, this._input);
};

Radio.prototype._setup = function(group, input) {
  jQuery(this._input).wrap('<span style="position:relative;"></span>');
  jQuery(this._input).css({position: 'absolute', left: '-9999em'});
  
  if (this._input.checked) jQuery(this._label).addClass('checked');
  
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
}

jQuery(document).ready(function() {
  new Radios('#tarski-headers input[type=radio]');
});
