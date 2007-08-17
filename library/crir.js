/*
	CRIR - Checkbox & Radio Input Replacement
	Author: Chris Erwin (me[at]chriserwin.com)
	www.chriserwin.com/scripts/crir/

	Update August 13th, 2007
	Major re-wrote by zbobet2012 to make script truly cross browser
	compatible including Internet Explorer, Firefox, and Safari. Selection
	event now added to label instead of checkbox.

	Updated July 27, 2006.
	Jesse Gavin added the AddEvent function to initialize
	the script. He also converted the script to JSON format.
	
	Updated July 30, 2006.
	Added the ability to tab to elements and use the spacebar
	to check the input element. This bit of functionality was
	based on a tip from Adam Burmister.
*/

crir = {
	init: function() {
		headerSection = document.getElementById("tarski-headers");
		//alert("headerSection length: " + headerSection.length);
		arrLabels = headerSection.getElementsByTagName('label');
		//alert("arrLabel length: " + arrLabels.length);
	
		searchLabels:
		for (var i=0; i<arrLabels.length; i++) {			
			// get the input element based on the for attribute of the label tag
			if (arrLabels[i].getAttributeNode('for') && arrLabels[i].getAttributeNode('for').value != '') {
				labelElementFor = arrLabels[i].getAttributeNode('for').value;				
				inputElement = document.getElementById(labelElementFor);
			}
			else {				
				continue searchLabels;
			}	

			inputElementClass = inputElement.className;	
		
			// if the input is specified to be hidden intiate it
			if (inputElementClass == 'crirHiddenJS') {
				inputElement.className = 'crirHidden';
				
				inputElementType = inputElement.getAttributeNode('type').value;	
				
				// add the appropriate event listener to the label for each element so that IE and Safari can use this
				if (inputElementType == "checkbox") {
					arrLabels[i].onclick = function(){crir.clickedCheckBoxLabel(this);};
				}
				else {
					arrLabels[i].onclick = function(){crir.clickedRadioLabel(this);};
				}
				
				// set the initial label state
				if (inputElement.checked) {
					if (inputElementType == 'checkbox') { arrLabels[i].className = 'checkbox_checked'}
					else { arrLabels[i].className = 'radio_checked' }
				}
				else {
					if (inputElementType == 'checkbox') { arrLabels[i].className = 'checkbox_unchecked'}
					else { arrLabels[i].className = 'radio_unchecked' }
				}
			}
			else if (inputElement.nodeName != 'SELECT' && inputElement.getAttributeNode('type').value == 'radio') { // this so even if a radio is not hidden but belongs to a group of hidden radios it will still work.
				arrLabels[i].onclick = function(){crir.clickedRadioLabel(this);};
				inputElement.onclick = function(){crir.toggleRadioLabel(this,crir.findLabel(this.getAttributeNode('id').value));};
			}
		}			
	},	

	//returns the laba for the specified inputElementId
	findLabel: function (inputElementID) {
		//arrLabels = document.getElementsByTagName('label');
	
		searchLoop:
		for (var i=0; i<arrLabels.length; i++) {
			if (arrLabels[i].getAttributeNode('for') && arrLabels[i].getAttributeNode('for').value == inputElementID) {				
				return arrLabels[i];
				break searchLoop;				
			}
		}		
	},
	
	//returns the input with the id specified by labelElementFor
	findInput: function (labelElementFor) {
		arrInputs = document.getElementsByTagName('input');
		searchLoop:
		for (var i=0; i<arrInputs.length; i++) {
			if (arrInputs[i].getAttributeNode('id') && arrInputs[i].getAttributeNode('id').value == labelElementFor) {	
				return arrInputs[i];
				break searchLoop;				
			}
		}		
	},		
	
	toggleCheckboxLabel: function (callingElement,labelElement) {
		//check/uncheck the "real" box
		callingElement.checked=!callingElement.checked;
		
		//check/uncheck the image
		if(labelElement.className == 'checkbox_checked') {
			labelElement.className = "checkbox_unchecked";
		}
		else {
			labelElement.className = "checkbox_checked";
		}
	},	
	
	toggleRadioLabel: function (clickedInputElement,clickedLabelElement) {			 

		clickedInputElementName = clickedInputElement.getAttributeNode('name').value;
		
		//get all me inputs so I can uncheck them
		tarskiHeader = document.getElementById("tarski-headers");
		arrInputs = tarskiHeader.getElementsByTagName('input');
		
		//check the radio button
		clickedInputElement.checked|=1;
		
		// uncheck (label class) all radios in the same group
		for (var i=0; i<arrInputs.length; i++) {			
			inputElementType = arrInputs[i].getAttributeNode('type').value;
			if (inputElementType == 'radio') {
				inputElementName = arrInputs[i].getAttributeNode('name').value;
				inputElementClass = arrInputs[i].className;
				// find radio buttons with the same 'name' as the one we've changed and have a class of chkHidden
				// and then set them to unchecked
				if (inputElementName == clickedInputElementName && inputElementClass == 'crirHidden') {				
					inputElementID = arrInputs[i].getAttributeNode('id').value;
					labelElement = crir.findLabel(inputElementID);
					labelElement.className = 'radio_unchecked';
				}
			}
		}
	
		// if the radio clicked is hidden set the label to checked
		if (clickedInputElement.className == 'crirHidden') {
			clickedLabelElement.className = 'radio_checked';
		}
	},
	
	clickedRadioLabel: function(callingElement){
		crir.toggleRadioLabel(crir.findInput(callingElement.getAttributeNode('for').value),callingElement);
	},
	
	clickedCheckBoxLabel: function(callingElement){
		crir.toggleCheckboxLabel(crir.findInput(callingElement.getAttributeNode('for').value),callingElement);
	},	
	
	addEvent: function(element, eventType, doFunction, useCapture){
		if (element.addEventListener) 
		{
			element.addEventListener(eventType, doFunction, useCapture);
			return true;
		} else if (element.attachEvent) {
			var r = element.attachEvent('on' + eventType, doFunction);
			return r;
		} else {
			element['on' + eventType] = doFunction;
		}
	}
}

crir.addEvent(window, 'load', crir.init, false);