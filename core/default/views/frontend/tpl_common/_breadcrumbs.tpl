
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
