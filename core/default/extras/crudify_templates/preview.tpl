{capture name=pagetitle}{t}Preview ENTITY_NICENAME{/t}{/capture}
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_header.tpl" pagetitle=$smarty.capture.pagetitle}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	<ul>
		<li>ID: {$ENTITY_NAME.ID_COLUMN_NAME}</li>
		<li>Created: {$ENTITY_NAME.created_on}</li>
		<li>Modified: {$ENTITY_NAME.modified_on}</li>
	</ul>
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a href="{url}EDIT_URL{/url}" class="button">
				<span class="rivety-button-icon rivety-button-icon ui-icon ui-icon-pencil"></span>
				{t}Edit{/t}
			</a>
		</li>
		<li>
			<a href="{url}DELETE_URL{/url}" class="button">
				<span class="rivety-button-icon rivety-button-icon ui-icon ui-icon-trash"></span>
				{t}Delete{/t}
			</a>
		</li>
		<li>
			<a href="{url}INDEX_URL{/url}" class="button">
				<span class="rivety-button-icon rivety-button-icon ui-icon ui-icon-close"></span>
				{t}Back{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_footer.tpl"}
