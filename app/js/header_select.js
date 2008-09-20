var Radios = function(selector) {
  this._elements = [];
  var radios = this;
  jQuery(selector).each(function(i) {
    radios._elements.push(new Radio(radios, this));
  });
};

var Radio = function(group, element) {
  this._input = element;
  this._label = document.getElementById('for_' + this._input.id);
  this._group = group;
  var radio = this;
  
  jQuery(this._input).wrap('<span style="position:relative;"></span>');
  jQuery(this._input).css({position: 'absolute', left: '-9999em'});
  
  if (this._input.checked)
    jQuery(this._label).addClass('checked');
  else
    jQuery(this._label).addClass('unchecked');
  
  jQuery([this._input, this._label]).click(function(event) {
    var i = radio._group._elements.length;
    
    while (i--) {
      radio._group._elements[i]._input.checked = false;
      jQuery(radio._group._elements[i]._label).removeClass('checked');
      jQuery(radio._group._elements[i]._label).addClass('unchecked');
    }
    
    radio._input.checked = true;
    jQuery(radio._label).removeClass('unchecked');
    jQuery(radio._label).addClass('checked');
  });
  
  jQuery(this._label).mouseover(function(event) {
    jQuery(this).addClass('hovered');
  });
  
  jQuery(this._label).mouseout(function(event) {
    jQuery(this).removeClass('hovered');
  });
};

jQuery(document).ready(function() {
  var radios = new Radios('#tarski-headers input');
});
