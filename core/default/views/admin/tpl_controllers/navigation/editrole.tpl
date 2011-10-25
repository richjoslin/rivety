{include file="file:$admin_theme_global_path/_header.tpl" pageTitle="Edit Navigation"}
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}/default/role/index{/url}">{t}Back to Roles{/t}</a></li>	
		<li>
			<a title="{t}Add New Link{/t}" href="{url}/default/navigation/edit/nav_id/0/role_id/{$role.id}{/url}">
				{t}Add New Link{/t}</a>
		</li>
	</ul>
</div>
<div id="main-column">
	<h3>{t}Navigation for{/t} {$role.shortname|capitalize}</h3>
	{* Notice: The next statement is including a recursive template. *}
	{include file="file:$current_path/_role_nav_tree.tpl" nav_items=$nav_items_to_edit}
</div>
{include file="file:$admin_theme_global_path/_footer.tpl"}
