{include file="file:$theme_global/_header.tpl" current="main_community"}
<div id="main-column">
	<h3>{t}Browse People{/t}</h3>
	{if count($users) gt 0}
		{foreach from=$users item=user key=index}
			<a href="{url}/profile/{$user.username}{/url}" title="{$user.username}">{$user.username}</a>
		{/foreach}
		{include file="file:$theme_global/_pager.tpl"}
	{else}
		<p>{t}No users found.{/t}</p>
	{/if}
	<h3>{t}Search People{/t}</h3>
	<form id="rivety-main-form" action="{url}/default/user/index/{/url}" method="post">

		<div class="rivety-form-field ui-corner-all">
			<label for="searchterm">Search</label>
			<input name="searchterm" id="searchterm" type="text" value="{$params.searchterm|replace:"-":" "}" class="text" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label>Sort by</label>
			<div class="buttonset"><input
				type="radio" name="sortby" id="sortby_updated" value="updated" {if $params.sortby eq "updated"}checked="checked"{/if}
				/><label for="sortby_updated">recently updated</label><input
				type="radio" name="sortby" id="sortby_login" value="login" {if $params.sortby eq "login"}checked="checked"{/if}
				/><label for="sortby_login">last login</label><input
				type="radio" name="sortby" id="sortby_newest" value="newest" {if $params.sortby eq "newest"}checked="checked"{/if}
				/><label for="sortby_newest">newest</label>
			</div>
		</div>

		<input type="submit" value="{t}Update{/t}" />

	</form>
</div>
<div id="options">
	<h3>Options</h3>
</div>
{include file="file:$theme_global/_footer.tpl"}
