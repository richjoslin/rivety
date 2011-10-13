<ul{if !$sublevel} class="tree"{/if}>
	{foreach from=$role_branches item=role key=the_role_id}
		<li>
			<label>
				<input name="inherit_role[]" value="{$the_role_id}" id="cb_role_{$the_role_id}"
					type="checkbox" {if is_array($inherited_ids)}{if in_array($the_role_id,$inherited_ids)} checked="checked"{/if}{/if}/>
				{$role.shortname}
			</label>
			{if count($role.children) gt 0}
				{include file="file:$current_path/_role_tree_recursive.tpl" role_branches=$role.children sublevel=true}
			{/if}	
		</li>
	{/foreach}
</ul>
