{include file="file:$theme_global/_header.tpl"}

{include file="file:$theme_global/_messages.tpl"}



	<div class="grid_12 alpha">
	<h3>{t}Edit your profile{/t}</h3>
	<form method="post" action="{url}/default/user/edit{/url}" enctype="multipart/form-data">

		<p>
			<label for="gender">{t}Gender{/t}</label><br />
			{html_options name=gender options=$genders selected=$user.gender}
		</p>
		<p>
			<label for="Birthday_Month">{t}Birthday{/t}</label><br />
			{html_select_date time=$user.birthday end_year=$end_year start_year=-100 month_value_format='%B' prefix='Birthday_'}
		</p>
		<p>
			<label for="country_code">{t}Country{/t}</label><br />
			{html_options name=country_code options=$countries selected=$user.country_code}
		</p>
		<h3>{t}Avatar{/t}</h3>		
		{include file="file:$theme_global/_user_image.tpl"}
		<br />
		<a href="/user/deleteavatar" title="{t}Delete Avatar{/t}">{t}Delete Avatar{/t}</a>
		<p>
			<label for="filedata">{t}Upload New Avatar{/t}</label><br />
			<input id="filedata" type="file" name="filedata"/>
		</p>
		<h3>{t}Account{/t}</h3>
		<p>
			<label for="full_name">{t}Full Name{/t}</label><br />
			<input maxlength="{$full_name_length}" type="text" value="{$user.full_name}" name="full_name" id="full_name" class="text{if count($errors.full_name) gt 0} LV_invalid_field{/if}" />
		</p>
		<p>
			<label for="email">{t}Email{/t}</label><br />
			<input type="text" value="{$user.email}" name="email" id="email" class="text" />
		</p>
		<h3>{t}About Me{/t}</h3>
		<p>
			<textarea cols="20" rows="6" name="aboutme" id="aboutme">{$user.aboutme}</textarea>
		</p>
		<h3>{t}Password{/t}</h3>
		<p>{t}(Leave blank for no change){/t}</p>
		<p>
			<label for="newpassword">New Password</label><br />
			<input type="password" value="" name="newpassword" id="newpassword" class="text" />
		</p>
		<p>
			<label for="confirm">New Password (again)</label><br />
			<input type="password" value="" name="confirm" id="confirm" class="text" />
		</p>
		<p class="no-border">
			<input type="submit" class="button" value="Submit" />
		</p>
		</form>
	</div>

	<div class="grid_4 omega">
		{include file="file:$theme_global/_user_nav.tpl"}
		<h3>{t}Cancel Account{/t}</h3>
		<div class="sidemenu">
		<ul><li><a href="{url}/user/cancel{/url}" title="{t}Cancel Account{/t}">{t}Cancel Account{/t}</a></li></ul>
		</div>
	</div>



{include file="file:$theme_global/_footer.tpl"}
