{include file="file:$current_path/_header.tpl" pageTitle="Installed!" masthead="Installation Complete"}
<div class="grid_16">
	<p>
		Congratulations! You've successfully installed communit.as!
	</p>
	<p>
		An administrator account has been created and a <b>random</b> password assigned:
	</p>
	<p>
		<b>Username:</b> {$username}<br />
		<b>Password:</b> {$password}
	</p>
	<p>
		You should make a note of this password, because this is the <i>last time</i> we're going to show it to you.
	</p>
	<h3>Things you could do right now</h3>
	<ul>
		<li><a href="/default/useradmin/edit/username/{$username}">Edit your profile or change your password.</a></li>
		<li><a href="/default/config/index">View or change configuration settings.</a></li>
		<li><a href="/default/useradmin/testdata">Load up some test data.</a></li>
		<li><a href="/">View the site.</a></li>
	</ul>
</div>
{include file="file:$current_path/_footer.tpl"}
