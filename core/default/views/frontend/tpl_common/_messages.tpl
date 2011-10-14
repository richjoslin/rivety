{if isset($success)}
<div class="success grid_16">	
	<p>{$success}</p>
</div>
<div class="clear"></div>
{/if}
{if isset($notice)}
<div class="notice grid_16">
	<p>{$notice}</p>
</div>
<div class="clear"></div>
{/if}
{if count($errors) gt 0}
<div class="error grid_16">
	<p><strong>{t}Error{/t}</strong></p>
	<ul>
		{foreach from=$errors item=error}
		<li>{$error}</li>
		{/foreach}
	</ul>
</div>
<div class="clear"></div>
{/if}
