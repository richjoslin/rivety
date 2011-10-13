{if count($pages) gt 1}
	<p class="pager">	
		{if isset($first)}<a title="First Page" class="pager-first" href="{url}{$pager_url}/page/{$first}{/url}">{t}First{/t}</a>{/if}
		{if isset($prev)}<a title="Previous Page" class="pager-previous" href="{url}{$pager_url}/page/{$prev}{/url}">{t}Previous{/t}</a>{/if}
		{if isset($next)}<a title="Next Page" class="pager-next" href="{url}{$pager_url}/page/{$next}{/url}">{t}Next{/t}</a>{/if}
		{if isset($last)}<a title="Last Page" class="pager-last" href="{url}{$pager_url}/page/{$last}{/url}">{t}Last{/t}</a>{/if}
	</p>
	<p class="pager">	
		<span class="pager-info">{t}Page{/t} {$display_page} {t}of{/t} {$total_pages}</span>
		<span class="pager-total">{$total} {t}results{/t}</span>
	</p>
{/if}
