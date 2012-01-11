{capture name=pagetitle}{t}Navigation for{/t} {$role.shortname|capitalize}{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pagetitle=$smarty.capture.pagetitle}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	{* Notice: The next statement is including a recursive template. *}
	{include file="file:$current_path/_role_nav_tree.tpl" nav_items=$nav_items_to_edit}
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a title="{t}Add New Link{/t}" href="{url}/default/navigation/edit/nav_id/0/role_id/{$role.id}{/url}" class="button">
				<span class="ui-icon ui-icon-plus" style="float: left; margin: 0 10px 0 0;">+</span>
				{t}Add Link{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
