{include file="file:$theme_global/_header.tpl"}
{include file="file:$theme_global/_screen_alerts.tpl"}
<div class="grid_12 alpha">
<h3>{t}Cancel Account{/t}</h3>

<p><b>{t}You are about to delete your account.{/t}</b></p>
<p>{t}This cannot be undone. All your information will be deleted.{/t}</p>	
{include file="file:$theme_global/_deleteform.tpl" d_url="/user/cancel"}		

</div>
<div class="grid_4 omega">
	{include file="file:$theme_global/_user_nav.tpl"}
</div>
{include file="file:$theme_global/_footer.tpl"}
