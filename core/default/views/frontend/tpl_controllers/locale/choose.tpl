{include file="file:$theme_global/_header.tpl"}
<div id="locale">
	<h2>Please select your country and language preferences</h2>
	<div class="region">
		<h3>North America</h3>
		<ul class="locales">
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
</div>
{include file="file:$theme_global/_footer.tpl"}
