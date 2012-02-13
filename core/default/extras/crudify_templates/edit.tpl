{capture name=pagetitle}{if !empty($ENTITY_NAME.ID_COLUMN_NAME)}{t}Edit{/t}{else}{t}New{/t}{/if} ENTITY_NICENAME{/capture}
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_header.tpl" pagetitle=$smarty.capture.pagetitle}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	<form id="FORM_ID" method="post">
		{if isset($ENTITY_NAME.ID_COLUMN_NAME)}
			<input type="hidden" name="ID_COLUMN_NAME" value="{$ENTITY_NAME.ID_COLUMN_NAME}" />
		{/if}
FORM_FIELDS
		<input type="submit" value="{t}Submit{/t}" />
	</form>
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a id="rivety-save-button" href="#" class="button">
				<span class="rivety-button-icon ui-icon ui-icon-disk"></span>
				{t}Save{/t}
			</a>
		</li>
		{if isset($ID_COLUMN_NAME)}
			<li>
				<a href="{url}DELETE_URL{/url}" class="button">
					<span class="rivety-button-icon ui-icon ui-icon-trash"></span>
					{t}Delete{/t}
				</a>
			</li>
		{/if}
		<li>
			<a href="{url}INDEX_URL{/url}" class="button">
				<span class="rivety-button-icon ui-icon ui-icon-close"></span>
				{t}Cancel{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_footer.tpl"}
