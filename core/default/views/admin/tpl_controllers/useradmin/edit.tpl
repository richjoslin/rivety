{capture name=pagetitle}{if empty($user.username)}New User{else}Edit User: {$user.username}{/if}{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle=$page_title css_urls=$smarty.capture.css_urls}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	<form id="rivety-admin-form" method="post" action="{url}/default/useradmin/edit/{/url}" autocomplete="off">

		{if !empty($user.username)}
			<input type="hidden" name="username" id="username" value="{$user.username}" />
		{else}
			<div class="rivety-form-field ui-corner-all">
				<label for="username">{t}Username{/t}</label>
				<input maxlength="50" type="text" name="username" id="username" />
			</div>
		{/if}

		<div class="rivety-form-field ui-corner-all">
			<label for="email">{t}Email{/t}</label>
			<input type="text" value="{$user.email}" name="email" id="email" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="role_id" class="full">{t}Role{/t}</label>
			{html_options
				name="role_ids[]"
				id="role_ids"
				options=$roles
				selected=$selected_roles
				multiple="multiple"
				size="10"
				class="chzn-select"}
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="newpassword">{if !$is_new}{t}New{/t} {/if}{t}Password{/t}</label>
			<input type="password" value="" name="newpassword" id="newpassword" />
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="confirm">{if !$is_new}{t}New{/t} {/if}{t}Password (again){/t}</label>
			<input type="password" value="" name="confirm" id="confirm" />
		</div>

		<input type="submit" value="{t}Save{/t}" />

	</form>
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a id="rivety-save-button" href="#" class="button" style="width: 158px;">
				<span class="rivety-button-icon ui-icon ui-icon-disk"></span>
				{t}Save{/t}
			</a>
		</li>
		{if !empty($user.username)}
			<li>
				<a href="{url}/default/useradmin/delete/username/{$user.username}/{/url}" class="button">
					<span class="rivety-button-icon ui-icon ui-icon-trash"></span>
					{t}Delete{/t}
				</a>
			</li>
		{/if}
		<li>
			<a class="button" href="{url}/default/useradmin/index/{/url}">
				<span class="rivety-button-icon ui-icon ui-icon-close"></span>
				{t}Cancel{/t}
			</a>
		</li>
	</ul>
</div>
{capture name=js_urls}
	{$admin_theme_url}/js/jq-picker.js
{/capture}
{include file="file:$admin_theme_path/tpl_common/_footer.tpl" js_urls=$smarty.capture.js_urls}
