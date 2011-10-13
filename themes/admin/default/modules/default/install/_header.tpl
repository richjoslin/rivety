<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Communit.as | Installation</title>
	<link rel="stylesheet" href="{$default_admin_theme_url}/css/reset.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="{$default_admin_theme_url}/css/text.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="{$default_admin_theme_url}/css/960.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="{$default_admin_theme_url}/css/default.css" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" media="screen" href="{$default_admin_theme_url}/css/superfish.css" />
	<!--[if IE]>
		<link rel="stylesheet" href="{$default_default_admin_theme_url}css/ie.css" type="text/css" media="screen, projection" />
	<![endif]-->	
	<script type="text/javascript" src="{$default_admin_theme_url}/js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="{$default_admin_theme_url}/js/hoverIntent.js"></script> 
	<script type="text/javascript" src="{$default_admin_theme_url}/js/superfish.js"></script> 
	{literal}
	<script type="text/javascript"> 
	    $(document).ready(function(){ 
	        $("ul.sf-menu").superfish(); 
	    }); 
	</script>
	{/literal}	
</head>
<body>
	<div id="header-wrap">		
		<div id="header" class="container_16">
		 	<div class="grid_12">		
				<h1>Communit.as</h1>
				<h2>Installation</h2>				
			</div>
			<div class="grid_4" id="util">
			</div>
		</div>	
	</div>
	<div id="content-wrap">
		<div id="content" class="container_16">	
			<p class="spacer">&nbsp;</p>
			{if isset($masthead)}
				<div class="grid_16" id="masthead"> 	
					<h2>{$masthead}</h2>
				</div>	
			{/if}
			{if isset($success)}
				<div class="status success grid_16">
					<div class="grid_16">	
					{$success}
					</div>
				</div>
			{/if}
			{if isset($notice)}
				<div class="status notice grid_16">
					<div class="grid_16">
					{$notice}
					</div>
				</div>
			{/if}
			{if count($errors) gt 0}
				<div class="status error grid_16">
					<div class="grid_16">
					Error
					</div>
				</div>
				<div class="error-list grid_16">
					<ul>
						{foreach from=$errors item=error}
						<li>{$error}</li>
						{/foreach}
					</ul>
				</div>
			{/if}	
			<!-- START: MAIN CONTENT AREA -->
