{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle="Plugin Hooks"}
<h1>{t}Manage{/t} <span class="white">{t}Plugins{/t}</span></h1>
{include file="file:$theme_global/admin/_screen_alerts.tpl"}
<h2>Filters</h2>
{foreach from=$hooks.filters item=hook key=hook_name}
	<h3>{$hook_name}</h3>
	<ul>
		{foreach from=$hook item=filter}
			<li>
				{$filter.class_name}::{$filter.function_name}
			</li>
		{/foreach}
	</ul>
{/foreach}
<h2>{t}Actions{/t}</h2>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
