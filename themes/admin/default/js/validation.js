
// XXX: assumes only one tips element is visible

var tips = $(".validateTips");

function updateTips(t)
{
	tips
		.text(t)
		.addClass("ui-state-highlight");
	setTimeout(function()
	{
		tips.removeClass("ui-state-highlight", 1500);
	}, 500);
}

function checkLength(o, n, min, max)
{
	if (o.val().length > max || o.val().length < min)
	{
		o.addClass("ui-state-error");
		updateTips("Length of " + n + " must be between " + min + " and " + max + ".");
		return false;
	}
	else
	{
		return true;
	}
}

function checkSelectionMade(o, n, unselectedValue)
{
	if (o.val() == unselectedValue)
	{
		o.addClass("ui-state-error");
		updateTips("Please choose " + n + ".");
		return false;
	}
	else
	{
		return true;
	}
}
