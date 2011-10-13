{if count($pages) gt 1}
<div class="pager{if isset($class)} {$class}{/if}">	
		{if isset($first)}<a title="First Page" class="pager-first" href="{$pager_url}/page/{$first}">First</a>{/if}
		{if isset($prev)}<a title="Previous Page" class="pager-previous" href="{$pager_url}/page/{$prev}">Previous</a>{/if}
		<span class="pager-info">Page {$display_page} of {$total_pages}</span>
		{if isset($next)}<a title="Next Page" class="pager-next" href="{$pager_url}/page/{$next}">Next</a>{/if}
		{if isset($last)}<a title="Last Page" class="pager-last" href="{$pager_url}/page/{$last}">Last</a>{/if}
		<span class="pager-total">{$total} {if isset($items_label)}{$items_label}{else}results{/if}</span>
		<br />	
</div>
{/if}