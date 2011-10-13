{include file="file:$theme_global/_header.tpl"}

<div class="title"><h1>Forgot your password?</h1></div>

<div id="main">
	<div class="main">
		<div class="sec">
			<div class="inner">
				{include file="file:$theme_global/_messages.tpl"}
				{if $showForm}
					<p>No problem. Happens to everyone. Just enter your email or your username below and we'll send you a link to change your password.</p>
					<form method="post" action="{url}/default/user/forgotpassword{/url}" enctype="multipart/form-data">
						<fieldset>			
							<div class="field">
								<label for="username">Username</label>
								<div class="input">
									<input type="text" value="{$username}" name="username" id="username" class="text" />
								</div>
							</div>
							<div class="field">
								<label for="email">Email</label>
								<div class="input">
									<input type="text" value="{$email}" name="email" id="email" class="text" />
								</div>
							</div>	
						</fieldset>									
						<div class="submit">			
							<button type="submit" name="confirm" value="Submit" class="sbm">Submit</button>		
						</div>
					</form>
				{/if}
			</div>
		</div>
	</div>
</div>
{include file="file:$theme_global/_footer.tpl"}