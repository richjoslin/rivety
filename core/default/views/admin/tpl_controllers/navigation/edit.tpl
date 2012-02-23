{capture name=pagetitle}Edit Link{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pagetitle=$smarty.capture.pagetitle}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	<form id="rivety-admin-form" action="{url}/default/navigation/edit/nav_id/{$nav_id}/role_id/{$role_id}{/url}" method="post">
		<input type="hidden" name="nav_id" value="{$nav_id}" />
		<input type="hidden" name="role_id" value="{$role_id}" />

		<div class="rivety-form-field ui-corner-all">
			<label for="parent_id">{t}Parent Item{/t}</label>
			<select name="parent_id" id="parent_id" class="chzn-select">
				<option value="0">{t}None{/t}</option>
				{include file="file:$admin_theme_path/tpl_common/_role_option.tpl" items=$parent_choices}
			</select>
		</div>

		<div class="rivety-form-field ui-corner-all">	
			<label for="short_name">{t}Short Name{/t}</label>
			<input type="text" name="short_name" id="short_name" value="{$short_name}" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="link_text">{t}Link Text{/t}</label>
			<input type="text" name="link_text" id="link_text" value="{$link_text}" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="url">{t}URL{/t}</label>
			<input type="text" name="url" id="url" value="{$url}" />
		</div>

		<input type="submit" value="{t}Save{/t}" />

	</form>
</div>
<div id="options">
	<h3>Options</h3>
	<ul>
		<li>
			<a id="rivety-save-button" href="#" class="button" style="width: 158px;">
				<span class="rivety-button-icon ui-icon ui-icon-disk"></span>
				{t}Save{/t}
			</a>
		</li>
		<li>
			<a class="button" href="{url}/default/navigation/editrole/id/{$role.id}{/url}">
				<span class="rivety-button-icon ui-icon ui-icon-close"></span>
				{t}Cancel{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
