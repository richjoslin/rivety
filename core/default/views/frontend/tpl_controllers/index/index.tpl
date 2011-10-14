{include file="file:$theme_global/_header.tpl" current="main_home"}
{include file="file:$theme_global/_messages.tpl"}
<div>
	{if $isLoggedIn ne true}
		<form action="{url}/default/auth/login{/url}" method="post" id="login">
			<p>
				<label for="txtUsername">Username</label><br />
				<input tabindex="1" type="text" name="username" id="txtUsername" value="{$LastLogin}" />
			</p>
			<p>
				<label for="txtPassword">Password</label><br />
				<input tabindex="2" type="password" name="password" id="txtPassword" value="" />
			</p>
			<p>
				<input tabindex="3" id="login" type="submit" value="{t}Login{/t}" />
			</p>
		</form>
	{/if}
</div>
{include file="file:$theme_global/_footer.tpl"}
