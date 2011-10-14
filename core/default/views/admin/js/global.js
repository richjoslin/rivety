
$(function()
{
	$('form').addClass("ui-widget");
	$('select').chosen();
	$('div.cms-accordion').accordion();
	rebuttonify();
	reapplyFancyboxLinks();
});

function rebuttonify()
{
	$("input[type=submit], button, .button").button();
}

function reapplyFancyboxLinks()
{
	$('a.fancybox').fancybox({
		'transitionIn': 'none',
		'transitionOut': 'none',
		'padding': 20,
		'overlayColor': '#fff',
		'overlayOpacity': 0.8
	});
}

function disableEnterKey()
{
	$('input').keypress(function(event){ return event.keyCode == 13 ? false : true; });
}

function enableEnterKey()
{
	$('input').keypress(function(event){ return true; });
}

function prettifyAllListButtons()
{
	$('a.list-button')
		.addClass('ui-state-default ui-corner-all')
		.hover(function(){
			$(this).addClass("ui-state-hover"); 
		}, function(){
			$(this).removeClass("ui-state-hover");  
		});
}
