{capture name=pageTitle}{if isset($role.id)}Edit Role {$role.shortname}{else}Add New Role{/if}{/capture}
{include file="file:$admin_theme_global_path/_header.tpl" masthead=$smarty.capture.pageTitle pageTitle=$smarty.capture.pageTitle} 
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li><a href="{url}/default/role/index{/url}">{t}Back to Roles{/t}</a></li>
		{if isset($role.id)}
			<li><a href="{url}/default/resource/edit/id/{$role.id}{/url}">{t}Resources{/t}</a></li>
			<li><a href="{url}/default/navigation/editrole/id/{$role.id}{/url}">{t}Navigation{/t}</a></li>
			<li><a href="{url}/default/role/delete/id/{$role.id}{/url}">{t}Delete Role{/t}</a></li>
		{/if}
	<ul>
</div>
<div id="main-column">
	<form action="{url}/default/role/edit{/url}" method="post">
		<p>
		    <label for="shortname">{t}Role Shortname{/t}</label>
		    <input type="text" name="shortname" value="{$role.shortname}"/>
	    </p>
	    <p>		    
		    <label for="description">{t}Description{/t}</label>
		    <input type="text" name="description" value="{$role.description}"/>
	    </p>
	    <p>
		    <label for="inherits_id" class="full">{t}Inherits{/t}</label>
			{*html_options name='inherits_id' options=$role_choices selected=$role.inherits_id*}			
		</p>
		{*
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
		{include file="file:$current_path/_role_tree_recursive.tpl" role_branches=$role_tree}
		<p><label>{t}Role Options{/t}</label></p>
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
		<p>
			<input type="hidden" name="id" value="{$role.id}" />
			<input type="submit" class="button save" value="{t}Save{/t}" />
		</p>
	</form>
</div>	
{include file="file:$admin_theme_global_path/_footer.tpl"}
