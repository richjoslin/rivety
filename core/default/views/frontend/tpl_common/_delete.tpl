{include file="file:$theme_path/tpl_common/_header.tpl" pagetitle=$pagetitle}
<div id="main-column">
	<h3>{$pagetitle}</h3>
	<p><b>{$delete_form_warning}</b></p>
	<p>{t}This can't be undone.{/t}</p>
	<p>{t}Are you sure you want to do this?{/t}</p>
	<form id="rivety-main-form" method="post" action="{$delete_form_action_url}">
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
				<span class="ui-icon ui-icon-close" style="float: left; margin: 0 10px 0 0;"></span>
				{t}Cancel{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$theme_path/tpl_common/_footer.tpl"}
