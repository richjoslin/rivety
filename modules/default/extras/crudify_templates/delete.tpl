{capture name=pagetitle}{t}Delete{/t}{/capture}
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_header.tpl" pagetitle=$smarty.capture.pagetitle}
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}INDEX_URL{/url}">{t}Back to List{/t}</a></li>
	</ul>
</div>
<div id="main-column">
	<h3>{t}Delete{/t}</h3>
	<p><b>{t}You are about to delete this item.{/t}</b></p>
	<p>{t}This cannot be undone.{/t}</p>	
	{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_deleteform.tpl" d_url="DELETE_URL/THE_ID/$THE_ID"}
</div>
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_footer.tpl"}
