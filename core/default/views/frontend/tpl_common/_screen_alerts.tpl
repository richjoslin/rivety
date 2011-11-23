{if isset($success) || isset($notice) || (isset($errors) && count($errors) gt 0)}
	<div id="messages" class="ui-widget ui-widget-content">

		{if isset($success)}
			<div class="ui-state-highlight ui-corner-all">
				<span class="ui-icon ui-icon-info"></span>
				<p>{$success}</p>
			</div>
		{/if}

		{if isset($notice)}
			<div class="ui-state-highlight ui-corner-all">
				<span class="ui-icon ui-icon-notice"></span>
				<p>{$notice}</p>
			</div>
		{/if}

		{if isset($errors)}
			{if count($errors) gt 0}
				{foreach from=$errors item=error}
					<div class="ui-state-error ui-corner-all">
						<span class="ui-icon ui-icon-alert"></span>
						<p>{$error}</p>
					</div>
				{/foreach}
			{/if}
		{/if}

	</div>
{/if}
