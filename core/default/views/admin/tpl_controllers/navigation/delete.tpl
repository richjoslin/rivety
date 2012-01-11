{capture name=pagetitle}{t}Delete Link{/t}: {$nav.link_text}{/capture}
{capture name=delete_form_warning}{t}You're about to delete this link.{/t}{/capture}
{capture name=delete_form_action_url}{url}/default/navigation/delete/nav_id/{$nav_id}/role_id/{$role_id}{/url}{/capture}
{capture name=delete_form_cancel_url}{url}/default/navigation/editrole/id/{$role_id}{/url}{/capture}
{* no need to edit below this *}
{include
	file="file:$admin_theme_global_path/_delete.tpl"
	pagetitle=$smarty.capture.pagetitle
	delete_form_warning=$smarty.capture.delete_form_warning
	delete_form_action_url=$smarty.capture.delete_form_action_url
	delete_form_cancel_url=$smarty.capture.delete_form_cancel_url
}
