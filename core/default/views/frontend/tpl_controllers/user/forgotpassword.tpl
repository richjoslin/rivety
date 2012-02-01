{include file="file:$theme_global/_header.tpl"}
<div id="main-coulmn">
	<h3>Forgot Password</h3>
	<form method="post" action="{url}/default/user/forgotpassword{/url}">
		<div class="rivety-form-field ui-corner-all">
			<label for="username">Username</label>
			<input type="text" value="{$username}" name="username" id="username" />
		</div>
		<div class="rivety-form-field ui-corner-all">
			<label for="email">Email</label>
			<input type="text" value="{$email}" name="email" id="email" />
		</div>	
		<input type="submit" value="submit" />
	</form>
</div>
{include file="file:$theme_global/_footer.tpl"}
