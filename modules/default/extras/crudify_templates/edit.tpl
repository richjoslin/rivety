{capture name=pagetitle}{if isset($id)}{t}Edit{/t}{else}{t}New{/t}{/if}{/capture}
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_header.tpl" pagetitle=$smarty.capture.pagetitle}
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}INDEX_URL{/url}">{t}Back to List{/t}</a></li>
		{if isset($THE_ID)}
			<li><a href="{url}DELETE_URL/THE_ID/{$THE_ID}/{/url}">{t}Delete{/t}</a></li>
			<li><a href="{url}CREATE_NEW_URL/{/url}">{t}Create Another{/t}</a></li>
		{/if}
	</ul>
</div>
<div id="main-column">
	<h3>{if isset($id)}{t}Edit{/t}{else}{t}New{/t}{/if}</h3>
	<form method="post" action="{url}FORM_ACTION{/url}" enctype="multipart/form-data">
		{if isset($THE_ID)}
			<input type="hidden" name="THE_ID" value="{$THE_ID}" />
		{/if}
		FORM_FIELDS
		<p>
			<input type="submit" class="button" value="{t}Save{/t}" />
		</p>
	</form>
</div>
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_footer.tpl"}
