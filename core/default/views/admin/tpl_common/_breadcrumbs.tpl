
<ul id="breadcrumbs">
	<li>
		<a href="{url}/default/admin/index{/url}">
			<span class="ui-icon ui-icon-home" style="float: left; margin: 5px 10px 0 0;"></span>
			{t}Dashboard{/t}
		</a>
	</li>
	{foreach from=$breadcrumbs item=breadcrumb}
		<li>
			{if $breadcrumb.url}
				<a href="{url}/grazia/recipeadmin/index{/url}">{$breadcrumb.text}</a>
			{else}
				{$breadcrumb.text}
			{/if}
		</li>
	{/foreach}
</ul>
