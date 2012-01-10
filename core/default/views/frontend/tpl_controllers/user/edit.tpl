{include file="file:$theme_global/_header.tpl"}
<div id="main-column">
	<h3>{t}Edit your profile{/t}</h3>
	<form method="post" action="{url}/default/user/edit{/url}" enctype="multipart/form-data">

		<div class="rivety-form-field ui-corner-all">
			<label for="email">{t}Email{/t}</label><br />
			<input type="text" value="{$user.email}" name="email" id="email" />
		</div>

		<h4>{t}Password{/t}</h4>

		<p>{t}(Leave blank for no change){/t}</p>

		<div class="rivety-form-field ui-corner-all">
			<label for="newpassword">New Password</label><br />
			<input type="password" value="" name="newpassword" id="newpassword" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="confirm">New Password (again)</label><br />
			<input type="password" value="" name="confirm" id="confirm" />
		</div>

		<input type="submit" value="{t}Submit{/t}" />

	</form>
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<div class="sidemenu">
		<ul>
			{include file="file:$theme_global/_user_nav.tpl"}
			<li>
				<a href="{url}/default/user/cancel{/url}" title="{t}Cancel My Account{/t}">{t}Cancel My Account{/t}</a>
			</li>
		</ul>
	</div>
</div>
{include file="file:$theme_global/_footer.tpl"}
