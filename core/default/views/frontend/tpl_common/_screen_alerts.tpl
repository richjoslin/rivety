{if !empty($screen_alerts) && count($screen_alerts) gt 0}
	<div id="messages" class="ui-widget ui-widget-content">
		{foreach from=$screen_alerts item=alert}
			{if $alert.type eq 'notice'}
				<div class="ui-state-highlight ui-corner-all">
					<span class="ui-icon ui-icon-notice"></span>
					<p>{$alert.message}</p>
				</div>
			{elseif $alert.type eq 'error'}
				<div class="ui-state-error ui-corner-all">
					<span class="ui-icon ui-icon-alert"></span>
					<p>{$alert.message}</p>
				</div>
			{else}
				<div class="ui-state-highlight ui-corner-all">
					<span class="ui-icon ui-icon-info"></span>
					<p>{$alert.message}</p>
				</div>
			{/if}
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
