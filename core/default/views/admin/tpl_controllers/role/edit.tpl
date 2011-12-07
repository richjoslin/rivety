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
		{*
		<p>
			<label for="inherits_id" class="full"></label>
			{ * html_options name='inherits_id' options=$role_choices selected=$role.inherits_id * }
		</p>
		<ul class="checkboxlist">
			{foreach from=$role_choices item=role_choice key=choice_id}
			<li>
				<label>
					<input type="checkbox" value="{$choice_id}" id="cbr_{$choice_id}" name="inherit_role[]" {if is_array($inherited_ids)}{if in_array($choice_id,$inherited_ids)} checked="checked"{/if}{/if}/>
					{$role_choice}
				</label>
			</li>
			{/foreach}
		</ul>
		*}
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
			<a class="button" href="{url}/default/role/index{/url}">
				<span class="ui-icon ui-icon-close" style="float: left; margin: 0 10px 0 0;"></span>
				{t}Cancel{/t}
			</a>
		</li>
		<li>
			<a id="rivety-save-button" href="#" class="button" style="width: 158px;">
				<span class="ui-icon ui-icon-disk" style="float: left; margin: 0 10px 0 0;"></span>
				{t}Save{/t}
			</a>
		</li>

		{if !empty($role) && isset($role.id)}
			<li><a href="{url}/default/resource/edit/id/{$role.id}{/url}">{t}Resources{/t}</a></li>
			<li><a href="{url}/default/navigation/editrole/id/{$role.id}{/url}">{t}Navigation{/t}</a></li>
			<li><a href="{url}/default/role/delete/id/{$role.id}{/url}">{t}Delete Role{/t}</a></li>
		{/if}
	<ul>
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
