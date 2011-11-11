{include file="file:$theme_global/_header.tpl"}
{include file="file:$theme_global/_messages.tpl"}
<div id="options">
	<h3>{t}Options{/t}</h3>
	{if $isLoggedIn ne true}
		<form action="{url}/default/auth/login{/url}" method="post">
			<div>
				<label for="txtUsername">{t}Username{/t}</label><br />
				<input tabindex="1" type="text" name="username" id="txtUsername" value="{$LastLogin}" />
			</div>
			<div>
				<label for="txtPassword">{t}Password{/t}</label><br />
				<input tabindex="2" type="password" name="password" id="txtPassword" value="" />
			</div>
			<input class="button" type="submit" value="{t}Login{/t}" />
		</form>
	{/if}
</div>
<div id="main-column">
	Content goes here.
</div>
{include file="file:$theme_global/_footer.tpl"}
