var currentlyManagingFolder = null;

$(function(){
	$("#photo-tabs").tabs();

	$("button.upload").click(function(event)
	{
		event.preventDefault();
		currentlyManagingFolder = $(this).parent();
		$("#upload-photos-modal-form").dialog("open");
	});

	$("button.delete").click(function(event)
	{
		event.preventDefault();
		currentlyManagingFolder = $(this).parent();
		$("#delete-photos-modal-form").dialog("open");
	});

	$("#upload-photos-modal-form").dialog(
	{
		autoOpen: false, width: 400, height: 150, modal: true, resizable: false,
		buttons:
		{
			Cancel: function(){ $(this).dialog("close"); },
			"Upload Photos": function()
			{
				var folderName = currentlyManagingFolder.attr("id");
				$("form", this).attr("action", $("form", this).attr("action") + "#" + folderName);
				$("form", this).submit();
			}
		},
		open: function()
		{
			var folderName = currentlyManagingFolder.attr("id");
			$("#upload_to_folder").val(folderName);
		},
		close: function()
		{
			$("input[type='file'], input[type='hidden']", this).val("");
			currentlyManagingFolder = null;
		}
	});

	$("#delete-photos-modal-form").dialog(
	{
		autoOpen: false, width: 800, height: 500, modal: true, resizable: false,
		buttons:
		{
			Cancel: function(){ $(this).dialog("close"); },
			"Delete Photos": function()
			{
				var folderName = currentlyManagingFolder.attr("id");
				$("form", this).attr("action", $("form", this).attr("action") + "#" + folderName);
				$("form", this).submit();
			}
		},
		open: function()
		{
			var folderName = currentlyManagingFolder.attr("id");
			$("#delete_from_folder").val(folderName);
			var fieldset = $("fieldset", this);
			$("div.gallery a", currentlyManagingFolder).each(function()
			{
				var newItem = $(this).clone();
				newItem.removeAttr("id");
				newItem.removeAttr("target");
				newItem.attr("href", "#");
				newItem.attr("class", "not-marked-for-deletion");
				fieldset.append(newItem);
			});
			$("a", this).click(function()
			{
				$(this).attr("class", $(this).attr("class") == "marked-for-deletion" ? "not-marked-for-deletion" : "marked-for-deletion");
				var filesToDeleteArray = [];
				var filesToDelete = $("#files_to_delete").val();
				if (filesToDelete !== "") filesToDeleteArray = filesToDelete.split(",");
				var img = $($("img", this)[0]);
				filesToDeleteArray.push(img.attr("id"));
				$("#files_to_delete").val(filesToDeleteArray.join(","));
			});
		},
		close: function()
		{
			$("fieldset a", this).remove();
			$("input[type='text'], input[type='hidden']", this).val("");
			currentlyManagingFolder = null;
		}
	});

});
