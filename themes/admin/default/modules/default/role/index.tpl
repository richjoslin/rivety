{capture name=pagetitle}{t}Manage Roles{/t}{/capture}
{include file="file:$admin_theme_global_path/_header.tpl" pagetitle=$smarty.capture.pagetitle}
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>		
		<li><a title="{t}Add New Role{/t}" href="{url}/default/role/edit{/url}">{t}Add New Role{/t}</a></li>
	</ul>
</div>
<div>
	{if count($roles) gt 0}
		<table>
			<thead>
				<tr>
					<th>{t}Role{/t}</th>
					<th>{t}Resources{/t}</th>
					<th>{t}Navigation{/t}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$roles item=role key=index}
					<tr class="{if ($index + 1) mod 2 eq 0}even{else}odd{/if}">
						<td>{if $role.shortname ne "guest"}<a href="{url}/default/role/edit/id/{$role.id}{/url}">{$role.shortname}</a>{else}{$role.shortname}{/if}</td>
						<td><a href="{url}/default/resource/edit/id/{$role.id}{/url}">{t}Resource Permissions{/t}</a></td>
						<td><a href="{url}/default/navigation/editrole/id/{$role.id}{/url}">{t}Customize Navigation{/t}</a></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}
</div>
{include file="file:$admin_theme_global_path/_footer.tpl"}
