{include file="file:$theme_global/_header.tpl"}
<div class="grid_4 alpha">
	{include file="file:$theme_global/_user_image.tpl"}
	<dl id="user-stats">
		<dt>{t}Name{/t}:</dt>			
		<dd>{$user.full_name}</dd>			
		{if $user.gender ne "unspecified"}
			<dt>{t}Gender{/t}:</dt>
			<dd>{$user.gender|capitalize}</dd>{/if}
		{if isset($user.age)}
			<dt>{t}Age:{/t}
			<dd>{$user.age} {t}years old{/t}</dd>				
		{/if}
		{if isset($user.location)}
			<dt>{t}Location:{/t}</dt>
			<dd>{$user.location|escape}</dd>
		{/if}
	</dl>		
</div>
<div class="grid_8">
	<h2>{$user.username}</h2>
	<h3>About</h3>
	{if $user.aboutme ne ""} 			
		<p>{$user.aboutme}</p>
	{else}
		<p><i>{t}Empty{/t}</i></p>
	{/if}
</div>
<div class="grid_4 omega">
{include file="file:$theme_global/_user_nav.tpl"}
</div>
{include file="file:$theme_global/_footer.tpl"}
