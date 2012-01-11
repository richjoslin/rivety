{capture name=pagetitle}Module Configuration{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle=$smarty.capture.pagetitle}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	<h4>Module: <span style="text-transform: uppercase;">{$module_title}</span></h4>
	{if count($config) gt 0}
		<form id="rivety-admin-form" method="post" action="{url}/default/config/index/modid/{$modid}{/url}">
			{foreach from=$config item=setting}
				<div class="rivety-form-field ui-corner-all">
					<label for="{$setting.ckey}">{$setting.ckey|replace:'_':' '|capitalize}</label>
					<input type="text" id="{$setting.ckey}" name="{$setting.ckey}" value="{$setting.value}" />
				</div>
			{/foreach}
			<input type="submit" value="{t}Save{/t}" />
		</form>
	{/if}
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	{if isset($modules)}
		<ul>
			<li>
				<a id="rivety-save-button" href="#" class="button">
					<span class="ui-icon ui-icon-disk" style="float: left; margin: 0 10px 0 0;"></span>
					{t}Save{/t}
				</a>
			</li>
		</ul>
	{/if}
</div>
<div id="jump-menu">
	<h3>{t}Modules{/t}</h3>
	<ul>
		{foreach from=$modules item=module}
			<li>
				<a class="button" href="{url}/default/config/index/modid/{$module}{/url}" style="font-size: 0.8em; text-transform: uppercase;">
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
