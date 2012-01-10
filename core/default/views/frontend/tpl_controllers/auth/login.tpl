{include file="file:$theme_global/_header.tpl"}
<div id="main-column">
	<h3>{t}Log In{/t}</h3>
	<form id="rivety-main-form" action="{url}/default/auth/login/{/url}" method="post">

		{if isset($ourl) && !empty($ourl)}
			<input type="hidden" name="ourl" id="ourl" value="{$ourl}" />
		{/if}

		<div class="rivety-form-field ui-corner-all">
			<label for="txtUsername">Username</label>
			<input type="text" name="username" id="txtUsername" value="{$LastLogin}" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="txtPassword">Password</label><br />
			<input type="password" name="password" id="txtPassword" value="" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label>Response Format</label>
			<div class="buttonset">
				<input type="radio" name="format" id="format_html" value="html" checked="checked" /><label for="format_html">HTML</label>
				<input type="radio" name="format" id="format_json" value="json" /><label for="format_json">JSON</label>
			</div>
		</div>

		<input id="login" type="submit" value="{t}Log In{/t}"/>

	</form>
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a href="{url}/default/user/forgotpassword/{/url}" title="{t}Reset your password{/t}">{t}Reset your password{/t}</a>
		</li>
		<li>
			<a id="rivety-save-button" href="#" class="button" style="width: 158px;">
				<span class="ui-icon ui-icon-key" style="float: left; margin: 0 10px 0 0;"></span>
				{t}Log In{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$theme_global/_footer.tpl"}
