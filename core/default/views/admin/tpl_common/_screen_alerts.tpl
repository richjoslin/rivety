{if !empty($screen_alerts) && count($screen_alerts) gt 0}
	<div id="messages" class="ui-widget ui-widget-content">
		{foreach from=$screen_alerts item=alert}
			<div class="ui-state-highlight ui-corner-all">
				{if $alert.type eq 'notice'}
					<span class="ui-icon ui-icon-notice"></span>
				{elseif $alert.type eq 'error'}
					<span class="ui-icon ui-icon-alert"></span>
				{else}
					<span class="ui-icon ui-icon-info"></span>
				{/if}
				<p>{$alert.message}</p>
			</div>
		{/foreach}
	</div>
{/if}

{* LEAVING THE DEPRECATED VERSION HERE SHOULDN'T HURT *}

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
