{include file="file:$admin_theme_global_path/_header.tpl" pageTitle=$pagetitle}
<div id="options">
	<ul>
		<li><a href="{url}/default/role/index{/url}">{t}Back to Roles{/t}</a></li>
	</ul>
</div>
<div id="main-column">
	<h3>{$pagetitle}</h3>
	<form action="{url}/default/navigation/edit/nav_id/{$nav_id}/role_id/{$role_id}{/url}" method="post">
		<p>
			<label for="parent_id">{t}Parent Item{/t}</label>
			<select name="parent_id" id="parent_id">
				<option value="0">{t}None{/t}</option>
				{include file="file:$current_path/_role_option.tpl" items=$parent_choices}
			</select>
		</p>
		<p>	
			<label for="short_name">{t}Short Name{/t}</label>
			<input name="short_name" id="short_name" value="{$short_name}" />
		</p>
		<p>
			<label for="link_text">{t}Link Text{/t}</label>
			<input name="link_text" id="link_text" value="{$link_text}" />
		</p>
		<p>
			<label for="url">{t}URL{/t}</label>
			<input name="url" id="url" value="{$url}" />
		</p>
		<p>
			<input type="hidden" name="nav_id" value="{$nav_id}" />
			<input type="hidden" name="role_id" value="{$role_id}" />
		</p>
		<p><input type="submit" class="button save" value="{t}Save{/t}" /></p>
	</form>
</div>
{include file="file:$admin_theme_global_path/_footer.tpl"}
