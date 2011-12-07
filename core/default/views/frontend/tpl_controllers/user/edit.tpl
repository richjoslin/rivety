{include file="file:$theme_global/_header.tpl"}
{include file="file:$theme_global/_screen_alerts.tpl"}
<div id="main-column">
	<h3>{t}Edit your profile{/t}</h3>
	<form method="post" action="{url}/default/user/edit{/url}" enctype="multipart/form-data">

		<div class="rivety-form-field ui-corner-all">
			<label for="gender">{t}Gender{/t}</label><br />
			{html_options name=gender options=$genders selected=$user.gender}
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="Birthday_Month">{t}Birthday{/t}</label><br />
			{html_select_date time=$user.birthday end_year=$end_year start_year=-100 month_value_format='%B' prefix='Birthday_'}
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="country_code">{t}Country{/t}</label><br />
			{html_options name=country_code options=$countries selected=$user.country_code}
		</div>

		<h4>{t}Avatar{/t}</h4>

		{include file="file:$theme_global/_user_image.tpl"}

		<a href="{url}/default/user/deleteavatar{/url}" title="{t}Delete Avatar{/t}">{t}Delete Avatar{/t}</a>

		<div class="rivety-form-field ui-corner-all">
			<label for="filedata">{t}Upload New Avatar{/t}</label><br />
			<input id="filedata" type="file" name="filedata"/>
		</div>

		<h4>{t}Account{/t}</h4>

		<div class="rivety-form-field ui-corner-all">
			<label for="full_name">{t}Full Name{/t}</label><br />
			<input maxlength="{$full_name_length}" type="text" value="{$user.full_name}" name="full_name" id="full_name" class="text{if count($errors.full_name) gt 0} LV_invalid_field{/if}" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="email">{t}Email{/t}</label><br />
			<input type="text" value="{$user.email}" name="email" id="email" class="text" />
		</div>

		<h4>{t}About Me{/t}</h4>

		<div class="rivety-form-field ui-corner-all">
			<textarea cols="20" rows="6" name="aboutme" id="aboutme">{$user.aboutme}</textarea>
		</div>

		<h4>{t}Password{/t}</h4>

		<p>{t}(Leave blank for no change){/t}</p>

		<div class="rivety-form-field ui-corner-all">
			<label for="newpassword">New Password</label><br />
			<input type="password" value="" name="newpassword" id="newpassword" class="text" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="confirm">New Password (again)</label><br />
			<input type="password" value="" name="confirm" id="confirm" class="text" />
		</div>

		<input type="submit" class="button" value="{t}Submit{/t}" />

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
