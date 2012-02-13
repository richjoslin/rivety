{capture name=pagetitle}{t}Delete{/t} ENTITY_NICENAME{/capture}
{capture name=delete_form_warning}
	<p>{t}You're about to delete this ENTITY_NICENAME_LOWERCASE.{/t}</p>
	<p>{t}This cannot be undone.{/t}</p>
{/capture}
{capture name=delete_form_action_url}{url}DELETE_URL{/url}{/capture}
{capture name=delete_form_cancel_url}{url}INDEX_URL{/url}{/capture}
{* no need to edit below this *}
{include
	file="file:$THEME_GLOBAL_PATH_VAR_NAME/_delete.tpl"
	pagetitle=$smarty.capture.pagetitle
	delete_form_warning=$smarty.capture.delete_form_warning
	delete_form_action_url=$smarty.capture.delete_form_action_url
	delete_form_cancel_url=$smarty.capture.delete_form_cancel_url
}
