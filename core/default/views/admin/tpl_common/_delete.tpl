{include file="file:$admin_theme_global_path/_header.tpl" pagetitle=$pagetitle}
<div id="main-column">
	<h3>{$pagetitle}</h3>
	<div>
		{$delete_form_warning}
	</div>
	<form id="rivety-admin-form" method="post"
		action="{$delete_form_action_url}">
		<input type="hidden" name="delete" value="{t}Yes{/t}" />
		<input type="submit" value="Confirm Delete" />
	</form>
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a id="rivety-save-button" href="#" class="shiny-red button">{t}Confirm Delete{/t}</a>
		</li>
		<li>
			<a href="{$delete_form_cancel_url}" class="button">
				<span class="rivety-button-icon ui-icon ui-icon-close"></span>
				{t}Cancel{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$admin_theme_global_path/_footer.tpl"}
