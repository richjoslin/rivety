{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle="Manage Users" masthead="Manage Users"}
<div id="options">
	<h3>{t}Search{/t}</h3>
	<form class="search" action="{url}/default/useradmin/index{/url}" method="post" id="usersearch">
		<p><input type="text" id="searchterm" name="searchterm" value="{$searchterm}"/></p>
		<p><input type="submit" value="{t}Search{/t}" class="button"/></p>
	</form>
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}/default/useradmin/edit{/url}">{t}Create New User{/t}</a></li>
	</ul>
</div>
<div id="main-column">
	{if count($users) gt 0}
		<table>
			<thead>
				<tr>
					<th>{t}Username{/t}</th>
					<th>{t}Full Name{/t}</th>
					<th>{t}Last Login{/t}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$users item=user key=index}
					<tr class="{if ($index + 1) mod 2 eq 0} even{else} odd{/if}">
						<td><a href="{url}/default/useradmin/edit/username/{$user.username}{/url}">{$user.username}</a></td>
						<td>{$user.full_name}</td>
						<td>{$user.last_login_on}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<p>{t}No users found.{/t}</p>
	{/if}
	{include file="file:$admin_theme_path/tpl_common/_pager.tpl"}
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
