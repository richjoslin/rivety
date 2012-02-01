{capture name=pagetitle}{t}Manage All Users{/t}{/capture}
{capture name=css_urls}
	/core/default/views/admin/tpl_controllers/useradmin/index.css
{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle=$smarty.capture.pagetitle css_urls=$smarty.capture.css_urls}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	{if count($users) gt 0}
		<table class="ui-widget">
			<thead>
				<tr class="ui-widget-header">
					<th>{t}Username{/t}</th>
					<th>{t}Last Login{/t}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$users item=user key=index}
					<tr class="{if ($index + 1) mod 2 eq 0} even{else} odd{/if}">
						<td><a href="{url}/default/useradmin/edit/username/{$user.username}{/url}">{$user.username}</a></td>
						<td>{$user.last_login_on}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<p>{t}No users found.{/t}</p>
	{/if}
	{include file="file:$admin_theme_path/tpl_common/_pager.tpl"}
	{*
	<h3>{t}Search Users{/t}</h3>
	<form action="{url}/default/useradmin/index/{/url}" method="post" id="usersearch">
		<div class="rivety-form-field ui-corner-all">
			<label for="searchterm">{t}Keyword{/t}</label>
			<input type="text" id="searchterm" name="searchterm" value="{$searchterm}"/>
		</div>
		<input type="submit" value="{t}Search{/t}" />
	</form>
	*}
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a href="{url}/default/useradmin/edit{/url}" class="button">
				<span class="rivety-button-icon ui-icon ui-icon-plus">{t}Add{/t}</span>
				{t}User{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
