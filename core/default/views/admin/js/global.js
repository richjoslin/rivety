
$(function()
{
	// $('form').addClass("ui-widget");
	// $('select').chosen();
	$('div.cms-accordion').accordion();
	$('.buttonset').buttonset();
	rebuttonify();
	reapplyFancyboxLinks();
	photoModal();
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

function photoModal()
{
	$("#photo-modal-picker").dialog(
	{
		autoOpen: false,
		width: 300,
		height: 700,
		modal: true,
		buttons:
		{
			Cancel: function(){ $(this).dialog("close"); }
		},
		open: function(event, ui)
		{
			disableEnterKey();
			var photoFolder = $(event.target).data('photoFolder');
			var photoField = $(event.target).data('photoField');
			var htmlArea = $(event.target).data('htmlArea');
			var htmlAreaOptions = $(event.target).data('htmlAreaOptions');

			$.getJSON("/default/photoadmin/list/folder/" + photoFolder, function(data)
			{
				for (var i = 0; i < data.length; i++)
				{
					var photoThumb = $("<img />");
					photoThumb.attr("src", "/displayimage/" + username + "/" + photoFolder + "/80/80/1/jpg/" + data[i]);
					var filenameSpan = $("<span />");
					filenameSpan.html(data[i]);
					var photoLink = $("<a />");
					photoLink.attr("href", "#");
					var photoListItem = $("<div />");
					photoListItem.attr("class", "photo-picker-list-item");
					photoLink.append(photoThumb);
					photoLink.append(filenameSpan);
					photoListItem.append(photoLink);
					$("#photo-modal-picker").append(photoListItem);
				}
				$("div.photo-picker-list-item a").click(function(event)
				{
					event.preventDefault();
					var imageFileName = $("span", this).html();
					if (htmlArea)
					{
						if (htmlAreaOptions)
						{
							// TODO: use the options to build the displayimage URL
						}
						// insert image - resized to width of 280 - maintain aspect ratio - do not crop - jpg extension
						htmlArea.image('/displayimage/' + username + '/' + photoFolder + '/280/0/0/jpg/' + imageFileName);
					}
					else
					{
						$(photoField).val(imageFileName);
					}
					$("#photo-modal-picker").dialog("close");
				});
			});
			$(event.target).removeData('photoFolder');
			$(event.target).removeData('photoField');
			$(event.target).removeData('htmlArea');
		},
		close: function()
		{
			$("#photo-modal-picker").empty();
			enableEnterKey();
		}
	});
}

function photoPicker(folderName, fieldId)
{
	$("#photo-modal-picker").data('photoFolder', folderName);
	$("#photo-modal-picker").data('photoField', fieldId);
	$("#photo-modal-picker").dialog("open");
}
