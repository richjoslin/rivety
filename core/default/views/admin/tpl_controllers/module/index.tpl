{include file="file:$admin_theme_global_path/_header.tpl" pageTitle="Manage Modules"}
<div id="main-column">
	{if count($modules) gt 0}
		<table>
			<thead>
				<tr>
					<th>{t}Module{/t}</th>
					<th>{t}Version{/t}</th>
					<th>{t}Status{/t}</th>
					<th>{t}Author{/t}</th>
					<th>{t}Description{/t}</th>
					<th>{t}Actions{/t}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$modules item=module key=index}
					<tr class="{if ($index + 1) mod 2 eq 0}even{else}odd{/if}">
						<td>{$module.general.name}</td>
						<td>{$module.general.version}</td>
						<td>
							{if $module.available}
								<span title="{t}Not Installed{/t}">{t}Not installed{/t}</span>
							{elseif $module.is_enabled eq 1}
								<span title="{t}Active{/t}">{t}Active{/t}</span>
							{elseif $module.is_enabled eq 0}
								<span title="{t}Disabled{/t}">{t}Disabled{/t}</span>
							{/if}
						</td>
						<td>
							{if isset($module.general.url)}
								<a href="{$module.general.url}">{$module.general.author}</a>
							{else}
								{$module.general.url}
							{/if}
						</td>
						<td>{$module.general.description}</td>
						<td>
							{if $module.available}
								<a href="{url}/default/module/index/id/{$module.id}/perform/install{/url}" title="{t}Install{/t}">{t}Install{/t}</a>
							{elseif $module.is_enabled eq 1}
								<a href="{url}/default/module/index/id/{$module.id}/perform/disable{/url}" title="{t}Disable{/t}">{t}Disable{/t}</a>
								<a href="{url}/default/module/uninstall/id/{$module.id}{/url}" title="{t}Uninstall{/t}">{t}Uninstall{/t}</a>
							{elseif $module.is_enabled eq 0}
								<a href="{url}/default/module/index/id/{$module.id}/perform/enable{/url}" title="{t}Enable{/t}">{t}Enable{/t}</a>
								<a href="{url}/default/module/uninstall/id/{$module.id}{/url}" title="{t}Uninstall{/t}">{t}Uninstall{/t}</a>
							{/if}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}
</div>
{include file="file:$admin_theme_global_path/_footer.tpl"}
