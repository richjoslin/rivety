{capture name=pageTitle}{if isset($role.id)}Edit Role {$role.shortname}{else}Add New Role{/if}{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle=$smarty.capture.pageTitle}
<div id="main-column">
	<h3>{$smarty.capture.pageTitle}</h3>
	<form action="{url}/default/role/edit{/url}" method="post" id="rivety-admin-form">
		{if !empty($role)}<input type="hidden" name="id" value="{$role.id}" />{/if}

		<div class="rivety-form-field ui-corner-all">
			<label for="shortname">{t}Role Shortname{/t}</label>
			<input type="text" name="shortname" value="{$role.shortname}"/>
		</div>

		<div class="rivety-form-field ui-corner-all">
			<label for="description">{t}Description{/t}</label>
			<input type="text" name="description" value="{$role.description}"/>
		</div>

		<h4>{t}Inherits{/t}</h4>

		<div class="rivety-form-field ui-corner-all">
			{include file="file:$current_path/_role_tree_recursive.tpl" role_branches=$role_tree}
		</div>

		<h4>{t}Role Options{/t}</h4>

		<div class="rivety-form-field ui-corner-all">
			<ul class="checkboxlist">
				{if $role.isguest eq 0}
					<li>
						<label>
							<input type="checkbox" value="1" name="isadmin" id="isadmin"{if $role.isadmin eq 1} checked="checked"{/if}/>
							{t}Admin{/t}
						</label>
					</li>
				{/if}
			</ul>
		</div>

		<input type="submit" class="button save" value="{t}Save{/t}" />
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
		{if !empty($role) && isset($role.id)}
			<li>
				<a class="button" href="{url}/default/role/delete/id/{$role.id}{/url}">
					<span class="rivety-button-icon ui-icon ui-icon-trash"></span>
					{t}Delete{/t}
				</a>
			</li>
		{/if}
		<li>
			<a class="button" href="{url}/default/role/index{/url}">
				<span class="rivety-button-icon ui-icon ui-icon-close"></span>
				{t}Cancel{/t}
			</a>
		</li>
	<ul>
</div>
{if !empty($role) && isset($role.id)}
	<div id="jump-menu">
		<h3>Jump To...</h3>
		<ul>
			<li><a class="button" href="{url}/default/resource/edit/id/{$role.id}{/url}">{t}Resources{/t}</a></li>
			<li><a class="button" href="{url}/default/navigation/editrole/id/{$role.id}{/url}">{t}Navigation{/t}</a></li>
		</ul>
	</div>
{/if}
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
