{if !empty($breadcrumbs)}
	<ul id="breadcrumbs">
		<li>
			<a href="{url}/{/url}"><span class="ui-icon ui-icon-home"></span>&nbsp;</a>
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

{*
<ul id="breadcrumbs">
	<li>
		<a href="{url}/{/url}">
			<span class="ui-icon ui-icon-home"></span>
			{t}Home{/t}
		</a>
	</li>
	{foreach from=$breadcrumbs item=breadcrumb}
		<li>
			{if $breadcrumb.url}
				<a href="{$breadcrumb.url}">{$breadcrumb.text}</a>
			{else}
				{$breadcrumb.text}
			{/if}
		</li>
	{/foreach}
</ul>
*}
