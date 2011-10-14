{include file="file:$admin_theme_global_path/_header.tpl" pageTitle="Delete Link"}
<div class="grid_4 sidenav">
	<ul>
		<li><a title="{t}Back to List{/t}" href="{url}/default/navigation/editrole/id/{$role_id}{/url}">{t}Back to List{/t}</a></li>
		<li><a title="{t}Add New Link{/t}" href="{url}/default/navigation/edit/nav_id/0/role_id/{$role_id}{/url}">{t}Add New Link{/t}</a></li>
	</ul>
</div>
<div class="grid_12">
	{if $can_delete}
		<p><b>{t}You are about to delete the link{/t} "{$nav.link_text}"</b></p>
		{capture name="d_url"}{url}/default/navigation/delete/nav_id/{$nav_id}/role_id/{$role_id}{/url}{/capture}
		{include file="file:$admin_theme_global_path/_deleteform.tpl" d_url=$smarty.capture.d_url}
	{/if}
</div>	
{include file="file:$admin_theme_global_path/_footer.tpl"}
