{capture name=pagetitle}{t}Add/Edit User{/t}{/capture}
{capture name=css_urls}
	{$admin_theme_url}/css/jq-picker.css
{/capture}
{capture name=js_urls}
	{$admin_theme_url}/js/jq-picker.js
{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl"
	pageTitle="Edit User `$user.username`" masthead="Edit User `$user.username`"
	pageTitle=$page_title
	js_urls=$smarty.capture.js_urls css_urls=$smarty.capture.css_urls}
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}/default/useradmin/index{/url}">{t}Back to Users{/t}</a></li>
		{if $success}
			<li><a href="{url}/default/useradmin/edit/username/{$user.username}{/url}">{t}Continue Editing{/t}</a></li>
		{else}
			<li><a href="{url}/default/useradmin/delete/username/{$user.username}{/url}">{t}Delete user{/t}</a></li>
		{/if}
	</ul>
</div>
<div id="main-column">
	{if !$success}
		<form method="post" action="{url}/default/useradmin/edit/username/{$user.username}{/url}" enctype="multipart/form-data">
			{if $is_new}
				<p>
					<label for="username">{t}Username{/t}</label>
					<input maxlength="24" type="text" name="username" id="username" />
				</p>
			{/if}
			<p>
				<label for="full_name">{t}Full Name{/t}</label>
				<input maxlength="{$full_name_length}" type="text" value="{$user.full_name}" name="full_name" id="full_name" class="text{if count($errors.full_name) gt 0} LV_invalid_field{/if}" />
			</p>
			<p>	
				<label for="email">{t}Email{/t}</label>
				<input type="text" value="{$user.email}" name="email" id="email" class="text" />
			</p>
			<p>	
				<label for="gender">{t}Gender{/t}</label>
				{html_options name=gender options=$genders selected=$user.gender}					
			</p>
			<p>		
				<label>{t}Birthday{/t}</label>
				{html_select_date time=$user.birthday end_year=$end_year start_year=-100 month_value_format='%B' prefix='Birthday_' class="datepicker"}
			</p>
			<p>
				<label for="country">{t}Country{/t}</label>
				{html_options name=country_code options=$countries selected=$user.country_code}
			</p>
			<p>
				<label for="aboutme">{t}About Me{/t}</label>
				<textarea name="aboutme" id="aboutme">{$user.aboutme}</textarea>
			</p>
			<p>
				<label for="role_id" class="full">{t}Role{/t}</label><p>
				{html_options
					name="role_ids[]"
					id="role_ids"
					options=$roles
					selected=$selected_roles
					multiple="multiple"
					size="10"
					class="jq-picker"}
			</p>
			<p>
				<label for="newpassword">{if !$is_new}{t}New{/t} {/if}{t}Password{/t}</label>							
				<input type="password" value="" name="newpassword" id="newpassword" class="text" />
			</p>
			<p>
				<label for="confirm">{if !$is_new}{t}New{/t} {/if}{t}Password (again){/t}</label>
				<input type="password" value="" name="confirm" id="confirm" class="text" />
			</p>
			<p><input type="submit" class="button save" value="{t}Save{/t}" /></p>
		</form>
	{/if}
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
