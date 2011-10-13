{if isset($success)}
	<div class="status success">
		{$success}
	</div>
{/if}

{if isset($notice)}
	<div class="status notice">
		{$notice}
	</div>
{/if}

{if count($errors) gt 0}
	<div class="status error">
		{t}Error{/t}
	</div>
	<div class="error-list">
		<ul>
			{foreach from=$errors item=error}
				<li>{$error}</li>
			{/foreach}
		</ul>
	</div>
{/if}
