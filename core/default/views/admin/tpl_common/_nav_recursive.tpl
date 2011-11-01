{foreach from=$items item=item}
	<li>
		<a href="{if isset($item.url)}{url}{$item.url}{/url}{else}#" class="no-href{/if}">{$item.link_text}</a>
		{if count($item.children) gt 0}
			<ul class="subnav">
				{include file="file:$admin_theme_path/tpl_common/_nav_recursive.tpl" items=$item.children}
			</ul>
		{/if}
	</li>
{/foreach}
