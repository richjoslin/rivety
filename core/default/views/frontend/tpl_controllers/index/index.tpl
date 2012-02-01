{include file="file:$theme_global/_header.tpl"}
<style>
	{literal}
		#rivety-sidebar-form input[type="text"],
		#rivety-sidebar-form input[type="password"],
		#rivety-sidebar-form input.button
		{
			width: 150px;
		}
	{/literal}
</style>
<div id="main-column">
	&nbsp;
</div>
<div id="options">
	<h3>{t}Log In{/t}</h3>
	{if $isLoggedIn ne true}
		<form id="rivety-sidebar-form" action="{url}/default/auth/login{/url}" method="post">
			<ul>
				<li>
					<label for="txtUsername">{t}Username{/t}</label>
					<input type="text" name="username" id="txtUsername" value="{$LastLogin}" />
				</li>
				<li>
					<label for="txtPassword">{t}Password{/t}</label>
					<input type="password" name="password" id="txtPassword" value="" />
				</li>
				<li>
					<input class="button" type="submit" value="{t}Log In{/t}" />
				</li>
			</ul>
		</form>
	{/if}
</div>
{include file="file:$theme_global/_footer.tpl"}
