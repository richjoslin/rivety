{include file="file:$admin_theme_path/tpl_common/_header.tpl"
	masthead=$page_title pageTitle=$page_title current="default_admin_serialize"}
<div class="grid_16">
	{if $results ne null}
		<ul>
			{foreach from=$results item=result key=index}
				<li>{$result}</li>
			{/foreach}
		</ul>
		<a href="{url}/default/admin/unserialize{/url}">{t}Back{/t}</a>
	{else}
		<form action="{url}/default/admin/unserialize{/url}" method="post">
			<p>
				<input type="text" name="serialized_array" id="serialized_array" />
			</p>
			<p>
				<input type="submit" value="{t}Submit{/t}" />
			</p>
		</form>
	{/if}
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
