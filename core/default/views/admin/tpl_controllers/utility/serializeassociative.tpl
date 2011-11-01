{include file="file:$admin_theme_path/tpl_common/_header.tpl"
	masthead=$page_title pageTitle=$page_title current="default_admin_serializeassociative"}
<div class="grid_16">
	{if $result ne null}
		<p>
			<textarea>{$result}</textarea>
		</p>
		<a href="{url}/default/admin/serializeassociative{/url}">{t}Back{/t}</a>
	{else}
		<form action="{url}/default/admin/serializeassociative{/url}" method="post">
			<p>
				<input type="text" name="key1" id="key1" />
				<input type="text" name="value1" id="value1" />
			</p>
			<p>
				<input type="text" name="key2" id="key2" />
				<input type="text" name="value2" id="value2" />
			</p>
			<p>
				<input type="text" name="key3" id="key3" />
				<input type="text" name="value3" id="value3" />
			</p>
			<p>
				<input type="text" name="key4" id="key4" />
				<input type="text" name="value4" id="value4" />
			</p>
			<p>
				<input type="text" name="key5" id="key5" />
				<input type="text" name="value5" id="value5" />
			</p>
			<p>
				<input type="text" name="key6" id="key6" />
				<input type="text" name="value6" id="value6" />
			</p>
			<p>
				<input type="text" name="key7" id="key7" />
				<input type="text" name="value7" id="value7" />
			</p>
			<p>
				<input type="submit" value="{t}Submit{/t}" />
			</p>
		</form>
	{/if}
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
