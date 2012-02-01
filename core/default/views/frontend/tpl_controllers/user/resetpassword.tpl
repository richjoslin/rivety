{include file="file:$theme_global/_header.tpl"}
<div id="main-column">
	<h3>Reset Your Password</h3>
	<form method="post" action="{url}/user/resetpassword/code/{$code}/email/{$email}{/url}">
		<div class="rivety-form-field ui-corner-all">
			<label for="username">New Password</label>
			<input type="password" value="" name="new_password" id="new_password" class="text" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="email">(Again)</label>
			<input type="password" value="" name="confirm" id="confirm" class="text" />
		</div>
		<input type="submit" value="Submit" />
	</form>
</div>
{include file="file:$theme_global/_footer.tpl"}
