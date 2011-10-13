{include file="file:$theme_global/_header.tpl" class="choose" bg="locale1"}

<h1 class="logo short">Welcome to Black Diamond</h1>

<div id="locale">
	<h2>Please select your country and language preferences</h2>

	<div class="area"><h3>North America</h3>
		<ul class="locale">
		{foreach from=$choices item=country key=index}
			<li><img src="{$theme_url}/images/flag/png/{$country.code|lower}.png"/> <a href="#{$country.code}">{$country.name}</a>
				<ul class="languages">
					{foreach from=$country.languages item=language key=index}
						<li><a href="/locale/setcookie/code/{$language.code}-{$country.code}">{$language.name}</a></li>
					{/foreach}
				</ul>
			</li>
		{/foreach}
		</ul>
	</div>
	<br class="clear"/>
		
	<h3><a href="#">Find a Dealer <img src="{$theme_url}/images/icons/arrow-orange_right.png" /></a>


</h3>
</div>


{include file="file:$theme_global/_footer.tpl"}
