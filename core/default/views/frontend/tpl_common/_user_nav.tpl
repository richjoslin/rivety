{if $loggedInUsername eq $user.username}
	<li><a href="{url}/default/user/profile{/url}" title="{t}View Profile{/t}" >{t}View Profile{/t}</a></li>
	<li><a href="{url}/default/user/edit{/url}" title="{t}Edit Account{/t}" >{t}Edit My Profile{/t}</a></li>

	{* TODO: these missing include files will throw errors regardless of whether the condition is true or false *}

	{*
	{if $module_friends}
		{include file="file:$theme_global/_user_nav_me_friends.tpl"}
	{/if}
	{if $module_mail}
		{include file="file:$theme_global/_user_nav_me_mail.tpl"}
	{/if}
	*}
{else}
	<li><a href="{url}/profile/{$user.username}{/url}" title="{$user.username}'s {t}Profile{/t}" >{$user.username}'s {t}Profile{/t}</a></li>
	{*
	{if $module_friends}
		{include file="file:$theme_global/_user_nav_notme_friends.tpl"}
	{/if}
	{if $module_mail}
		<li><a href="{url}/mail/index/new/to/{$user.username}{/url}" title="{t}Send message to{/t} {$user.username}">{t}Send message to{/t} {$user.username}</a></li>
	{/if}
	*}
	{if $isAdmin}
		<li><a href="{url}/default/useradmin/edit/username/{$user.username}{/url}" title="{t}Edit User{/t}">{t}Edit User{/t}</a></li>
	{/if}
{/if}
