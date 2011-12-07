{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle=$page_title}
<div id="main-column">
	{if $result ne null}
		<p>
			<textarea>{$result}</textarea>
		</p>
		<a href="{url}/default/admin/serialize{/url}">{t}Back{/t}</a>
	{else}
		<form action="{url}/default/admin/serialize{/url}" method="post">
			<p>
				<input type="text" name="value1" id="value1" />
			</p>
			<p>
				<input type="text" name="value2" id="value2" />
			</p>
			<p>
				<input type="text" name="value3" id="value3" />
			</p>
			<p>
				<input type="text" name="value4" id="value4" />
			</p>
			<p>
				<input type="text" name="value5" id="value5" />
			</p>
			<p>
				<input type="text" name="value6" id="value6" />
			</p>
			<p>
				<input type="text" name="value7" id="value7" />
			</p>
			<p>
				<input type="submit" value="{t}Submit{/t}" />
			</p>
		</form>
	{/if}
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
