<div class="twobuttons">
	<form class="singlebutton" method="post" action="{url}{$d_url}{/url}" enctype="multipart/form-data">
		<input type="hidden" name="delete" value="Yes"/>
		<input type="submit" class="button" value="{t}Yes{/t}"/>					  
	</form>							

	<form class="singlebutton" method="post" action="{url}{$d_url}{/url}" enctype="multipart/form-data">
		<input type="hidden" name="delete" value="No"/>
		<input type="submit" class="button" value="{t}No{/t}"/>				      
	</form>	
	<div class="clear"></div>							
</div>