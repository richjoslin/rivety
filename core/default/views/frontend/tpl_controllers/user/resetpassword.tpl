{include file="file:$theme_global/_header.tpl"}

<div class="title"><h1>Password Reset</h1></div>

<div id="main">
	<div class="main">
		<div class="sec">
			<div class="inner">
				{include file="file:$theme_global/_screen_alerts.tpl"}
				<p>You can use this form to reset your password.</p>
				<form method="post" action="{url}/user/resetpassword/code/{$code}/email/{$email}{/url}" enctype="multipart/form-data">
					<fieldset>
						<div class="field">
							<label for="username">New Password</label>
							<div class="input">
								<input type="password" value="" name="new_password" id="new_password" class="text" />
							</div>
						</div>
						<div class="field">
							<label for="email">(Again)</label>
							<div class="input">
								<input type="password" value="" name="confirm" id="confirm" class="text" />
							</div>
						</div>	
					</fieldset>									
					<div class="submit">						
						<button type="submit" name="submit" value="Submit" class="sbm">Submit</button>		
					</div>
				</form>
				
			</div>
		</div>
	</div>
</div>
{include file="file:$theme_global/_footer.tpl"}