{include file="file:$theme_global/_header.tpl" current="main_community"}
<div class="grid_4 alpha">
	<form action="{url}/user{/url}" method="post" class="sidebar-form" id="search-user-form">
		<p>
			<label>Search</label><br />
			<input name="searchterm" type="text" value="{$params.searchterm|replace:"-":" "}" class="text" />
		</p>
		<p id="user-search-radios">
			<label>Sort by</label><br />
			<input type="radio" name="sortby" value="updated" {if $params.sortby eq "updated"}checked="checked"{/if} /> recently updated<br />
			<input type="radio" name="sortby" value="login" {if $params.sortby eq "login"}checked="checked"{/if} /> last login<br />
			<input type="radio" name="sortby" value="newest" {if $params.sortby eq "newest"}checked="checked"{/if} /> newest<br />
		</p>
		<p>
			<input type="submit" value="{t}Update{/t}" class="button"/>
		</p>
	</form>
</div>
<div class="grid_12 omega">
	<h2>{t}Browse People{/t}</h2>
	{if count($users) gt 0}
		<p class="thumbs">
			{foreach from=$users item=user key=index}
				<a href="{url}/profile/{$user.username}{/url}"
					title="{$user.username} ({$user.full_name}) :: {if $user.gender ne "unspecified"}{$user.gender|capitalize} /{/if} {$user.age} years old {if isset($user.location)}/ {$user.location|escape}{/if}" class="tips"><img width="100" height="100" src="/displayimage/{$user.username}/original/100/100/1/jpg/avatar" alt="" /></a>
			{/foreach}
		</p>
		{include file="file:$theme_global/_pager.tpl" class="grid_12 omega"}
	{else}
		<p>No users found.</p>
	{/if}
</div>
{include file="file:$theme_global/_footer.tpl"}
