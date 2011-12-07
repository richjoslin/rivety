{capture name=pagetitle}{t}Manage Roles{/t}{/capture}
{capture name=css_urls}
	/core/default/views/admin/tpl_controllers/role/index.css
{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle=$smarty.capture.pagetitle css_urls=$smarty.capture.css_urls}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	{if count($roles) gt 0}
		<table class="ui-widget">
			<thead>
				<tr class="ui-widget-header">
					<th>{t}Role{/t}</th>
					<th class="center">{t}Options{/t}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$roles item=role key=index}
					<tr class="{if ($index + 1) mod 2 eq 0}even{else}odd{/if}">
						<td>{if $role.shortname ne "guest"}<a href="{url}/default/role/edit/id/{$role.id}{/url}">{$role.shortname}</a>{else}{$role.shortname}{/if}</td>
						<td class="table-options center">
							<a href="{url}/default/resource/edit/id/{$role.id}{/url}">{t}Resource Permissions{/t}</a>
							<a href="{url}/default/navigation/editrole/id/{$role.id}{/url}">{t}Customize Navigation{/t}</a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>		
		<!-- <li><a title="{t}Add New Role{/t}" href="{url}/default/role/edit{/url}">{t}Role{/t}</a></li> -->
		<li>
			<a href="{url}/default/role/edit{/url}" class="button">
				<span class="ui-icon ui-icon-plus" style="float: left; margin: 0 10px 0 0;">{t}Add{/t}</span>
				{t}Role{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
