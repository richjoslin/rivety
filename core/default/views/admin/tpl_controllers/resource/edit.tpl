{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle="$module_title Resources for Role: $roleshortname"}
<div id="options">
	<h3>{t}Modules{/t}</h3>
	<ul>
		{foreach from=$modules item=module}
			<li><a href="{url}/default/resource/edit/id/{$role.id}/modid/{$module}{/url}">{$module}</a></li>
		{/foreach}	
	</ul>
	<ul>
		<li><a href="{url}/default/role/index{/url}">{t}Back to Roles{/t}</a><br /></li>
	</ul>
</div>
<div id="main-column">
	<h3>{$module_title} Resources for Role: {$roleshortname}</h3>
	<form action="{url}/default/resource/edit/id/{$role.id}/modid/{$modid}{/url}" method="post">
		{foreach from=$actions key=module_name item=module}
			{foreach from=$module key=controller_name item=controller}
				<h4>{$controller_name}</h4>
				<ul>
					{foreach from=$controller key=action_name item=action}
						<li>
							{if $action.inherited}
								<label><input type="checkbox" checked="checked" id="{$module_name}-{$controller_name}-{$action_name}" disabled="disabled"/> {$action_name} <i>({t}inherited{/t})</i></label>
							{else}
								<label><input type="checkbox" name="resource[]" id="{$module_name}-{$controller_name}-{$action_name}" value="{$module_name}-{$controller_name}-{$action_name}" {if $action.allowed}checked="checked"{/if}/> {$action_name}</label>
							{/if}
						</li>
					{/foreach}
				</ul>
			{/foreach}
		{/foreach}
		{if count($extra_resources) gt 0}
			<h4>{t}Extra{/t}</h4>
			<ul>
				{foreach from=$extra_resources item=extra_resource key=resource_name}
					<li>
						{if $extra_resource.inherited}
							<label><input type="checkbox" checked="checked" disabled="disabled"/> {$extra_resource.nicename} <i>({t}inherited{/t})</i></label>
						{else}
							<label><input type="checkbox" name="extra_resource[]" value="{$resource_name}" {if $extra_resource.allowed}checked="checked"{/if}/> {$extra_resource.nicename}</label>
						{/if}
					</li>
				{/foreach}
			</ul>
		{/if}
		<div class="final-actions">
			<div class="button-bar">
				<input type="submit" class="button" value="{t}Save{/t}" />
			</div>
		</div>
	</form>
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
