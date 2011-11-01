{include file="file:$admin_theme_path/tpl_common/_header.tpl"
	pageTitle="Delete User `$username`" masthead="Delete User `$username`"}
<div class="grid_4 sidenav">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}/default/useradmin/index{/url}">{t}Back to Users{/t}</a></li>
		{if !isset($success)}
			<li><a href="{url}/default/useradmin/delete/username/{$user.username}{/url}">{t}Delete User{/t}</a></li>
		{/if}
	</ul>
</div>
<div class="grid_12">
	{if !isset($success)}
		<p><b>{t}You are about to delete the user{/t} "{$username}"</b></p>
		{capture name="d_url"}
			{url}/default/useradmin/delete/username/{$username}{/url}
		{/capture}
		{include file="file:$admin_theme_path/tpl_common/_deleteform.tpl" d_url=$smarty.capture.d_url}
	{else}
		<p>&nbsp;</p>
	{/if}
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
