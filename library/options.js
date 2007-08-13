$(document).ready(function() {
	// Sidebar switching code - needs refactoring
	$('input#option-ts').change(function() {
		$('div#tarski-sidebar-section').show();
		$('div#widgets-sidebar-section').hide();
		$('div#custom-sidebar-section').hide();
	});
	$('input#option-ws').change(function() {
		$('div#tarski-sidebar-section').hide();
		$('div#widgets-sidebar-section').show();
		$('div#custom-sidebar-section').hide();
	});
	$('input#option-fs').change(function() {
		$('div#tarski-sidebar-section').hide();
		$('div#widgets-sidebar-section').hide();
		$('div#custom-sidebar-section').show();
	});
});