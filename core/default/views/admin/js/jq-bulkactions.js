/*
	File: Generic Bulk Checkbox Handling

	About: Compatibility
		Non-degrading - This does not work without JavaScript enabled.

	About: Author
		Rich Joslin <http://richjoslin.com>
*/
function initBulkCheckboxes() {
	// set up click event on the master checkbox
	$('#check_all').bind('change', function (e) {
		// check or uncheck all checkboxes
		$('.bulk_checkbox').attr('checked', $(this).attr('checked'));
		// trigger change events on all checkboxes
		$('.bulk_checkbox').change();
	});
	// set up submit event on the bulk action form
	$('#bulk_action_form').bind('submit', function () {
		// set the form action based on which bulk action option was selected
		// if the first option is selected, it means no action was chosen
		// and an alert should be shown
		if ($('#bulk_action').val() != '') {
			$(this).attr('action', $('#bulk_action').val());
			// if there are items checked, process them, otherwise show an alert
			if ($('.bulk_checkbox:checked').length) {
				// this assumes all the checkboxes are outside the form
				// upon submit, clone all checkboxes in the page
				// and move the clones into the bulk action form
				// so the values are included in the form request
				// make sure they're hidden so they don't cause
				// a weird layout blowout upon form on submit
				$('.bulk_checkbox').each(function () {
					var checkboxClone = $(this).clone();
					checkboxClone.hide();
					$('#bulk_action_form').append(checkboxClone);
				});
			} else {
				alert('Please check an item to be processed.');
				return(false);
			}
		} else {
			alert('Please choose a bulk action.');
			return(false);
		}
	});
}

$(document).ready(function () {
	initBulkCheckboxes();
});
