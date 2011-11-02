{include file="file:$theme_global/_header.tpl"}
{include file="file:$theme_global/_messages.tpl"}
<div>
	{if $isLoggedIn ne true}
		<form action="{url}/default/auth/login{/url}" method="post" id="login">
			<div class="rivety-form-field">
				<label for="txtUsername">{t}Username{/t}</label><br />
				<input tabindex="1" type="text" name="username" id="txtUsername" value="{$LastLogin}" />
			</div>
			<div class="rivety-form-field">
				<label for="txtPassword">{t}Password{/t}</label><br />
				<input tabindex="2" type="password" name="password" id="txtPassword" value="" />
			</div>
			<input tabindex="3" id="login" type="submit" value="{t}Login{/t}" />
		</form>
	{/if}
</div>
{include file="file:$theme_global/_footer.tpl"}
