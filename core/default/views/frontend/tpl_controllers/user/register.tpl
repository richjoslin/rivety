{include file="file:$theme_global/_header.tpl"}
<div id="main-column">
	<h3>Register</h3>
	<form id="rivety-main-form" method="post" autocomplete="off">

		<div class="rivety-form-field ui-corner-all">
			<label for="username">{t}Username{/t}</label>
			<input type="text" maxlength="{$username_length}" value="{$user.username}" name="username" id="username" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="password">{t}Password{/t}</label>
			<input type="password" maxlength="32" value="" name="password" id="password" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="confirm">{t}Password (again){/t}</label>
			<input type="password" maxlength="32" value="" name="confirm" id="confirm" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="email">{t}Email{/t}</label>
			<input type="text" value="{$user.email}" name="email" id="email" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label>Response Format</label>
			<div class="buttonset">
				<input type="radio" name="format" id="format_html" value="html" checked="checked" /><label for="format_html">HTML</label>
				<input type="radio" name="format" id="format_json" value="json" /><label for="format_json">JSON</label>
			</div>
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label>Options</label>
			<div class="buttonset">
				<input type="checkbox" name="autologin" id="autologin" value="true" checked="checked" /><label for="autologin">Automatic Login</label>
			</div>
		</div>

		<input type="submit" value="{t}Register{/t}" />

	</form>
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a id="rivety-save-button" href="#" class="button" style="width: 158px;">
				<span class="rivety-button-icon ui-icon ui-icon-plus"></span>
				{t}Submit{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$theme_global/_footer.tpl"}
