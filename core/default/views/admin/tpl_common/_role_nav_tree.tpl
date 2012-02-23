{* Notice: This template is recursive. *}
{if is_null($level)}
	{assign var=level value=0}
{/if}
{if count($nav_items) gt 0}
	{foreach from=$nav_items item=nav_item key=index name=linklist}

		<div class="admin-nav-tree ui-widget" style="float: left; clear: both; width: 360px; padding: 7px 0 4px 0; border-bottom: solid 1px #555;">

			<div style="float: left; margin-left: {math equation="$level * 40"}px;">
				{if $smarty.foreach.linklist.first}
					<span class="left ui-icon ui-icon-circle-arrow-n ui-state-disabled">
						{t}Move Up{/t}
					</span>
				{else}
					<a class="left ui-icon ui-icon-circle-arrow-n"
						href="{url}/default/navigation/moveup/nav_id/{$nav_item.id}/parent_id/{$nav_item.parent_id}/role_id/{$role.id}{/url}">
						<span>{t}Move Up{/t}</span>
					</a>
				{/if}
			</div>

			<div style="float: left;">
				{if $smarty.foreach.linklist.last}
					<span class="ui-icon ui-icon-circle-arrow-s ui-state-disabled">
						{t}Move Down{/t}
					</span>
				{else}
					<a class="ui-icon ui-icon-circle-arrow-s"
						href="{url}/default/navigation/movedown/nav_id/{$nav_item.id}/parent_id/{$nav_item.parent_id}/role_id/{$role.id}{/url}">
						<span>{t}Move Down{/t}</span>
					</a>
				{/if}
			</div>

			<div style="float: left; margin: 0 0 0 10px;">
				<span>{$nav_item.link_text}</span>
			</div>

			<div style="float: right;">
				<a href="{url}/default/navigation/delete/nav_id/{$nav_item.id}/role_id/{$role.id}{/url}">{t}Delete{/t}</a>
			</div>

			<div style="float: right; margin: 0 20px 0 0;">
				<a href="{url}/default/navigation/edit/nav_id/{$nav_item.id}/role_id/{$role.id}{/url}">{t}Edit{/t}</a>
			</div>

		</div>
		{include file="file:$admin_theme_path/tpl_common/_role_nav_tree.tpl" nav_items=$nav_item.children level=$level+1}
	{/foreach}
{/if}
