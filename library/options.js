$(document).ready(function() {
	// Sidebar switching code - needs refactoring
	$('input#option-ts').change(function() {
		$('div#tarski-sidebar-section').toggle();
		$('div#widgets-sidebar-section').toggle();
		$('div#custom-sidebar-section').toggle();
	});
	$('input#option-ws').change(function() {
		$('div#tarski-sidebar-section').toggle();
		$('div#widgets-sidebar-section').toggle();
		$('div#custom-sidebar-section').toggle();
	});
	$('input#option-fs').change(function() {
		$('div#tarski-sidebar-section').toggle();
		$('div#widgets-sidebar-section').toggle();
		$('div#custom-sidebar-section').toggle();
	});
	
})