{include file="file:$admin_theme_path/tpl_common/_header.tpl" pageTitle="Load Test Data"}
<div id="main-column">
	<form method="post" class="adminform" action="{url}/default/useradmin/testdata{/url}" enctype="multipart/form-data">
		<p>
			<label for="data_path">{t}Path to Test Data{/t}</label>
			<input type="text" value="{$data_path}" name="data_path" id="data_path" />
		</p>
		<p>
			<label for="email_domain">{t}Email Domain{/t}</label>
			<input type="text" value="{$email_domain}" name="email_domain" id="email_domain" />
		</p>
		<p class="clearfix"><input type="submit" class="button load" value="{t}Load{/t}" /></p>
	</form>
</div>
{include file="file:$admin_theme_path/tpl_common/_footer.tpl"}
