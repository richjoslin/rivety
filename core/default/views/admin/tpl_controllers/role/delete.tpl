{capture name=pagetitle}{t}Delete Role{/t}: {$role.name}{/capture}
{capture name=delete_form_warning}{t}You're about to delete this role.{/t}{/capture}
{capture name=delete_form_action_url}{url}/default/role/delete/id/{$role.id}{/url}{/capture}
{capture name=delete_form_cancel_url}{url}/default/role/edit/id/{$role.id}{/url}{/capture}
{* no need to edit below this *}
{include
	file="file:$admin_theme_global_path/_delete.tpl"
	pagetitle=$smarty.capture.pagetitle
	delete_form_warning=$smarty.capture.delete_form_warning
	delete_form_action_url=$smarty.capture.delete_form_action_url
	delete_form_cancel_url=$smarty.capture.delete_form_cancel_url
}
