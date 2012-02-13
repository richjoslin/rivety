{capture name=pagetitle}{t}Manage ENTITY_NICENAME Records{/t}{/capture}
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_header.tpl" pagetitle=$smarty.capture.pagetitle}
<div id="main-column">
	<h3>{$smarty.capture.pagetitle}</h3>
	{if !empty($ROWSET_VAR_NAME)}
		<table>
			<thead>
				<tr>
					<th>{t}ID_COLUMN_NAME{/t}</th>
					<th>{t}Column 2{/t}</th>
					<th>{t}Column 3{/t}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$ROWSET_VAR_NAME item=ENTITY_NAME key=index}
					<tr class="{if $index mod 2 eq 0}odd{else}even{/if}">
						<td><a href="{url}PREVIEW_URL{/url}">{$ENTITY_NAME.ID_COLUMN_NAME}</a></td>
						<td>foo</td>
						<td>bar</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<p>{t}No ENTITY_NICENAME_LOWERCASE found.{/t}</p>
	{/if}
	{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_pager.tpl"}
</div>
<div id="options">
	<h3>{t}Options{/t}</h3>
	<ul>
		<li>
			<a class="button" href="{url}CREATE_NEW_URL{/url}">
				<span class="rivety-button-icon ui-icon ui-icon-plus"></span>
				{t}Add New{/t}
			</a>
		</li>
	</ul>
</div>
{include file="file:$THEME_GLOBAL_PATH_VAR_NAME/_footer.tpl"}
