{if !empty($breadcrumbs)}
	<ul id="breadcrumbs">
		<li>
			<a href="{url}/default/admin/index/{/url}"><span class="ui-icon ui-icon-home"></span>&nbsp;</a>
		</li>
		{foreach from=$breadcrumbs item=url key=label}
			<li>
				{if empty($url)}
					{$label}
				{else}
					<a href="{$url}">{$label}</a>
				{/if}
			</li>
		{/foreach}
	</ul>
{/if}
