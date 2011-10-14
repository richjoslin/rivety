{include file="file:$admin_theme_global_path/_header.tpl" pageTitle="Configuration"}
{* $modid *}
<div id="options">
	<h3>{t}Modules{/t}</h3>
	{if isset($modules)}
		<ul>
			{foreach from=$modules item=mod}
				<li{if $current eq $mod} style="font-weight: bold; background: #ddd;" class="current"{/if}>
					<a href="{url}/default/config/index/modid/{$mod}{/url}">{$mod}</a>
				</li>
			{/foreach}
		</ul>
	{/if}
</div>
<div id="main-column">
	{if count($config) gt 0}
		<form method="post" action="{url}/default/config/index/modid/{$modid}{/url}">
			{foreach from=$config item=setting}
				<div class="form-field">
					<label for="{$setting.ckey}">{$setting.ckey|replace:'_':' '|capitalize}</label>
					<input type="text" id="{$setting.ckey}" name="{$setting.ckey}" value="{$setting.value}" />
				</div>
			{/foreach}
			<div class="final-actions">
				<div class="button-bar">
					<input type="submit" class="button save" value="{t}Save{/t}" />
				</div>
			</div>
		</form>
	{/if}
</div>
{include file="file:$admin_theme_global_path/_footer.tpl"}
