{include file="file:$current_path/_header.tpl"}
{if isset($success)}
	{if isset($config_file)}
		<textarea rows="20" cols="80">{$config_file}</textarea>
	{/if}
{else}
	<form method="post" action="/" class="multiform" enctype="multipart/form-data">

		<h3>Default Database Settings</h3>

		<div class="form-field">
			<label>Database Host</label>
			<input type="text" value="{$db_host}" name="db_host" id="db_host" class="text" />
		</div>
		<div class="form-field">
			<label>Database Port</label>
			<input type="text" value="{$db_port}" name="db_port" id="db_port" class="text" />
		</div>
		<div class="form-field">
			<label>Database Socket (optional)</label>
			<input type="text" value="{$db_sock}" name="db_sock" id="db_sock" class="text" />
		</div>
		<div class="form-field">
			<label>Database Name (database must already exist)</label>
			<input type="text" value="{$db_name}" name="db_name" id="db_name" />
		</div>
		<div class="form-field">
			<label>Database Username</label>
			<input type="text" value="{$db_user}" name="db_user" id="db_user" />
		</div>
		<div class="form-field">
			<label>Database Password</label>
			<input type="password" value="{$db_pass}" name="db_pass" id="db_pass" />
		</div>

		<h3>Application Settings</h3>

		<div class="form-field">
			<label>Time zone</label>
			{html_options name=cts_timezone id=cts_timezone options=$timezones selected=$cts_timezone}
		</div>

		<h3>Admin User Settings</h3>

		<div class="form-field">
			<label>Username</label>
			<input type="text" value="{$admin_username}" name="admin_username" id="admin_username" />
		</div>
		<div class="form-field">
			<p>
				(the password will be auto-generated, but you will have a chance to change it right away)
			</p>
		</div>
		<div class="form-field">
			<label>Email</label>
			<input type="text" value="{$admin_email}" name="admin_email" id="admin_email" />
		</div>

		<h3>Advanced Settings</h3>

		<p>
			Only change these if you know you need to.
		</p>

		<div class="form-field">
			<label>Path to Zend Framework</label>
			<input type="text" size="75" value="{$cts_zf_path}" name="cts_zf_path" id="cts_zf_path" />
		</div>
		<div class="form-field">
			<label>Path to Smarty</label>
			<input type="text" size="75" value="{$cts_smarty_path}" name="cts_smarty_path" id="cts_smarty_path" />
		</div>
		<div class="form-field">
			<label>Path to Asido</label>
			<input type="text" size="75" value="{$cts_asido_path}" name="cts_asido_path" id="cts_asido_path" />
		</div>
		<div class="form-field">
			<label>Temp Path</label>
			<input type="text" size="75" value="{$tmp_path}" name="tmp_path" id="tmp_path" />
		</div>
		<div class="form-field">
			<label>Log Path</label>
			<input type="text" size="75" value="{$log_path}" name="log_path" id="log_path" />
		</div>

		<input type="submit" value="Continue" class="button" />

	</form>
{/if}
{include file="file:$current_path/_footer.tpl"}
