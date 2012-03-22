{include file="file:$default_module_this_controller_path/_header.tpl"}

{if isset($success)}
	{if isset($config_file)}
		<textarea rows="20" cols="80">{$config_file}</textarea>
	{/if}
{else}

	<div id="main-column">
		<form method="post" action="/">

			<h3>Default Database Settings</h3>

			<div class="rivety-form-field ui-corner-all">
				<label>Database Host</label>
				<input type="text" value="{$db_host}" name="db_host" id="db_host" class="text" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Database Port</label>
				<input type="text" value="{$db_port}" name="db_port" id="db_port" class="text" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Database Socket (optional)</label>
				<input type="text" value="{$db_sock}" name="db_sock" id="db_sock" class="text" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Database Name (database must already exist)</label>
				<input type="text" value="{$db_name}" name="db_name" id="db_name" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Database Username</label>
				<input type="text" value="{$db_user}" name="db_user" id="db_user" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Database Password</label>
				<input type="password" value="{$db_pass}" name="db_pass" id="db_pass" />
			</div>

			<h3>Application Settings</h3>

			<div class="rivety-form-field ui-corner-all">
				<label>Time zone</label>
				{html_options name=timezone id=timezone options=$timezones selected=$timezone}
			</div>

			<h3>Admin User Settings</h3>

			<div class="rivety-form-field ui-corner-all">
				<label>Username</label>
				<input type="text" value="{$admin_username}" name="admin_username" id="admin_username" />
			</div>

			<p>
				(the password will be auto-generated, but you will have a chance to change it right away)
			</p>

			<div class="rivety-form-field ui-corner-all">
				<label>Email</label>
				<input type="text" value="{$admin_email}" name="admin_email" id="admin_email" />
			</div>

			<h3>Advanced Settings</h3>

			<p>
				Only change these if you know you need to.
			</p>

			<div class="rivety-form-field ui-corner-all">
				<label>Path to Zend Framework</label>
				<input type="text" size="75" value="{$zf_path}" name="zf_path" id="zf_path" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Path to Smarty</label>
				<input type="text" size="75" value="{$smarty_path}" name="smarty_path" id="smarty_path" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Path to Asido</label>
				<input type="text" size="75" value="{$asido_path}" name="asido_path" id="asido_path" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Temp Path</label>
				<input type="text" size="75" value="{$tmp_path}" name="tmp_path" id="tmp_path" />
			</div>

			<div class="rivety-form-field ui-corner-all">
				<label>Log Path</label>
				<input type="text" size="75" value="{$log_path}" name="log_path" id="log_path" />
			</div>

			<input type="submit" value="Continue" class="button" />

		</form>
	</div>

{/if}
{include file="file:$default_module_this_controller_path/_footer.tpl"}
