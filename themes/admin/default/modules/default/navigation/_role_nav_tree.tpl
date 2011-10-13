{* Notice: This template is recursive. *}
{if is_null($level)} 
	{assign var=level value=0}
{/if}
{if count($nav_items) gt 0}	
	<ul{if $level eq 0} class="editnav"{/if}>
		{foreach from=$nav_items item=nav_item key=index name=linklist}
			<li class="level-{$level}">
				<div class="buttons clearfix">
					<span class="linkname level-{$level}">{$nav_item.link_text|truncate:30}</span>
					{if $smarty.foreach.linklist.first}
						<span class=""></span>
					{else}
						<a class="up" href="{url}/default/navigation/moveup/nav_id/{$nav_item.id}/parent_id/{$nav_item.parent_id}/role_id/{$role.id}{/url}">{t}Move Up{/t}</a>
					{/if}
					{if $smarty.foreach.linklist.last}
						<span class=""></span>
					{else}
						<a class="down" href="{url}/default/navigation/movedown/nav_id/{$nav_item.id}/parent_id/{$nav_item.parent_id}/role_id/{$role.id}{/url}">{t}Move Down{/t}</a>
					{/if}
					<a class="edit" href="{url}/default/navigation/edit/nav_id/{$nav_item.id}/role_id/{$role.id}{/url}">{t}Edit{/t}</a>					
					<a class="delete" href="{url}/default/navigation/delete/nav_id/{$nav_item.id}/role_id/{$role.id}{/url}">{t}Delete{/t}</a>
				</div>
				{include file="file:$current_path/_role_nav_tree.tpl" nav_items=$nav_item.children level=$level+1}
			</li>
		{/foreach}
	</ul>
{/if}
