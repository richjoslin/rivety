
$(function()
{
	$('form').addClass("ui-widget");
	$('div.cms-accordion').accordion();
	$('.buttonset').buttonset();
	$('.chzn-select').chosen();
	rebuttonify();

	// if JavaScript is active,
	// hide the semantic save button
	// and wire up the sidebar save button which lives outside the form
	$('#rivety-main-form input[type="submit"]').hide();
	$('#rivety-save-button, #rivety-save-button').live('click', function(event)
	{
		event.preventDefault();
		$('#rivety-main-form').submit();
	});
});

function rebuttonify()
{
	$("input[type=submit], button, .button").button();
}

function disableEnterKey()
{
	$('input').keypress(function(event){ return event.keyCode == 13 ? false : true; });
}

function enableEnterKey()
{
	$('input').keypress(function(event){ return true; });
}
