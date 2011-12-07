{capture name=pagetitle}Resource Access Control{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle=$smarty.capture.pagetitle}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	<h4>Role: <span style="text-transform: uppercase;">{$roleshortname}</span></h4>
	<h4>Module: <span style="text-transform: uppercase;">{$module_title}</span></h4>

	<form action="{url}/default/resource/edit/id/{$role.id}/modid/{$modid}{/url}" method="post" id="rivety-admin-form">

		{foreach from=$actions key=module_name item=module}
			{foreach from=$module key=controller_name item=controller}
				<h4>{$controller_name}</h4>
				<div class="rivety-form-field ui-corner-all">
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
				</div>
			{/foreach}
		{/foreach}

		<h4>{t}Extra{/t}</h4>
		<div class="rivety-form-field ui-corner-all">
			{if count($extra_resources) gt 0}
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
			{else}
				N/A
			{/if}
		</div>

		<input type="submit" class="button" value="{t}Save{/t}" />
	</form>
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a href="{url}/default/role/index{/url}" class="button">
				<span class="ui-icon ui-icon-close" style="float: left; margin: 0 10px 0 0;"></span>
				{t}Cancel{/t}
			</a>
		</li>
		<li>
			<a id="rivety-save-button" href="#" class="button" style="width: 158px;">
				<span class="ui-icon ui-icon-disk" style="float: left; margin: 0 10px 0 0;"></span>
				{t}Save{/t}
			</a>
		</li>
	</ul>
</div>
<div id="jump-menu">
	<h3>{t}Modules{/t}</h3>
	<ul>
		{foreach from=$modules item=module}
			<li>
				<a class="button" href="{url}/default/resource/edit/id/{$role.id}/modid/{$module}{/url}" style="font-size: 0.8em; text-transform: uppercase;">
					{if $module eq 'default'}rivety core{else}{$module}{/if}
				</a>
			</li>
		{/foreach}	
	</ul>
	<span class="jump-warning">
		Warning: choosing a different module will lose any unsaved changes.
	</span>
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
