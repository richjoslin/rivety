{include file="file:$admin_theme_path/tpl_common/_header.tpl" mastHead="Uninstall Module" pageTitle="Uninstall Module"}
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}/default/module/index{/url}">{t}Back to list{/t}</a></li>
	</ul>
</div>
<div id="main-column">
	<p>{t}You are about to delete the module{/t} '{$id}'.</p>
	<p>{t}Make sure you have all the data for this module backed up somewhere.{/t}</p>
	<p>{t}All data related to this module will be deleted.{/t}</p>
	{capture name="d_url"}
		{url}/default/module/uninstall/id/{$id}{/url}
	{/capture}
	{include file="file:$admin_theme_path/tpl_common/_deleteform.tpl" d_url=$smarty.capture.d_url}
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
