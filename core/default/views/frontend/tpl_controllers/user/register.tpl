{include file="file:$theme_global/_header.tpl"}
{include file="file:$theme_global/_screen_alerts.tpl"}

	<div class="grid_6 suffix_10">
	<h3>{$pagetitle}</h3>
	
	<form method="post" action="{url}/default/user/register{/url}" enctype="multipart/form-data">
		
			<p>
				<label for="username">{t}Username{/t}</label><br />
				<input maxlength="{$username_length}" type="text" value="{$user.username}" name="username" id="username" class="text" />
			</p>
			<p>
				<label for="full_name">{t}Full Name{/t}</label><br />
				<input maxlength="{$full_name_length}" type="text" value="{$user.full_name}" name="full_name" id="full_name" class="text" />
			</p>			
			<p>
				<label for="password">{t}Password{/t}</label><br />
				<input type="password" maxlength="32" value="" name="password" id="password" class="text" />					
			</p>
			<p>
				<label for="confirm">{t}Password (again){/t}</label><br />
				<input type="password" maxlength="32" value="" name="confirm" id="confirm" class="text" />					
			</p>

			<p>
				<label for="email">{t}Email{/t}</label><br />
				<input type="text" value="{$user.email}" name="email" id="email" class="text" />					
			</p>
			<p class="birthday">
				<label>{t}Birthday{/t}</label><br />
				{html_select_date time=$user.birthday start_year=-100 month_value_format='%B' prefix='Birthday_'}					
			</p>		

			<p>
			<input type="submit" class="button" value="{t}Register{/t}">
			</p>					
	</form>
	</div>
{include file="file:$theme_global/_footer.tpl"}