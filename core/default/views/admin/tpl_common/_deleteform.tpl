<div class="delete-form">
	<p>{t}Are you <i>sure</i> you want to do this?{/t}</p>
	<div>
		<form method="post" class="yes-form" action="{$d_url}" enctype="multipart/form-data">
			<input type="hidden" name="delete" value="Yes" />
			<input type="submit" class="button yes" value="{t}Yes{/t}" />
		</form>
		<form method="post" class="no-form" action="{$d_url}" enctype="multipart/form-data">
			<input type="hidden" name="delete" value="No" />
			<input type="submit" class="button no" value="{t}No{/t}" />
		</form>		
	</div>	
</div>
