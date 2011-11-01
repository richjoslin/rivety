{include file="file:$theme_global/_header.tpl"}
{include file="file:$theme_global/_messages.tpl"}

<div class="grid_6 suffix_4 alpha">
<h3>{t}Log In{/t}</h3>
			<form action="{url}/default/auth/login{/url}" method="post">
				<p>	
					<label for="txtUsername">Username</label><br />
					<input tabindex="1" class="text" type="text" name="username" id="txtUsername" value="{$LastLogin}" />
				</p>
				<p>
					<label for="txtPassword">Password</label><br />
					<input tabindex="2" class="text" type="password" name="password" id="txtPassword" value="" />
				</p>
				<p><input id="login" type="submit" class="button" tabindex="3" value="{t}Log In{/t}"/></p>
			</form>

</div>


<div class="grid_6 omega">
		<div class="sidemenu">
		<h3>{t}Need an account?{/t}</h3>
		<ul><li><a href="{url}/default/user/register{/url}" title="{t}Register for an account{/t}">{t}Register for an account{/t}</a></li></ul>

		<h3>{t}Forget your password?{/t}</h3>
		<ul><li><a href="{url}/default/user/forgotpassword{/url}" title="{t}Reset your password{/t}">{t}Reset your password{/t}</a></li></ul>

		</div>
</div>

{include file="file:$theme_global/_footer.tpl"}
