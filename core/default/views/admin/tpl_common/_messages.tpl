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

{if isset($errors)}
	{if count($errors) gt 0}
		<div class="status error">
			Error
		</div>
		<div class="error-list">
			<ul>
				{foreach from=$errors item=error}
					<li>{$error}</li>
				{/foreach}
			</ul>
		</div>
	{/if}
{/if}
