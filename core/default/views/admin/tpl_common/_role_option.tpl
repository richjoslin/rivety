{if is_null($level)} 
	{assign var=level value=0}
{/if}
{foreach from=$items item=item}
	<option class="level-{$level}" value="{$item.id}"{if $item.id eq $parent_id} selected{/if}>{$item.link_text}</option>
	{if count($item.children) gt 0}
		{include file="file:$admin_theme_path/tpl_common/_role_option.tpl" items=$item.children level=$level+1}
	{/if}
{/foreach}
