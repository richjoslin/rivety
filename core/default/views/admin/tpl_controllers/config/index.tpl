{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle="Configuration"}
{* $modid *}
<div id="options">
	<h3>{t}Modules{/t}</h3>
	{if isset($modules)}
		<ul>
			{foreach from=$modules item=mod}
				<li{if $current eq $mod} style="font-weight: bold;"{/if}>
					<a href="{url}/default/config/index/modid/{$mod}{/url}">{$mod}</a>
				</li>
			{/foreach}
			<li>
				<a id="rivety-save-button" href="#" class="button">
					<span class="ui-icon ui-icon-disk" style="float: left; margin: 0 10px 0 0;"></span>
					{t}Save{/t}
				</a>
			</li>
		</ul>
	{/if}
</div>
<div id="main-column">
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
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
