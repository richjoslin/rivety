{include file="file:$admin_theme_global_path/_header.tpl" mastHead="Uninstall Module" pageTitle="Uninstall Module"}
<div class="grid_4 sidenav">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}/default/module/index{/url}">{t}Back to list{/t}</a></li>
	</ul>
</div>
<div class="grid_12">
	<p>{t}You are about to delete the module{/t} '{$id}'.</p>
	<p>{t}This will likely result in the deletion of all data associated with this module.{/t}</p>
	{capture name="d_url"}
		{url}/default/module/uninstall/id/{$id}{/url}
	{/capture}
	{include file="file:$admin_theme_global_path/_deleteform.tpl" d_url=$smarty.capture.d_url}
</div>
{include file="file:$admin_theme_global_path/_footer.tpl"}
