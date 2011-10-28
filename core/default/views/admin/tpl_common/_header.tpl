<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>{$title_prefix}{if isset($pageTitle)} | {$pageTitle}{/if}</title>
		<link type="text/css" rel="stylesheet" href="{$admin_theme_url}/css/screen.css" />
		<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="{$admin_theme_url}/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
		<!-- <link rel="stylesheet" type="text/css" href="{$admin_theme_url}/css/chosen/chosen.css" media="screen" /> -->
		<link rel="stylesheet" type="text/css" href="{$admin_theme_url}/css/jHtmlArea/jHtmlArea.css" media="screen" />
		{include_css urls=$css_urls}
	</head>
	<body>
		<!-- <div id="switcher" style="position: absolute; top: 0; left: 500px;"></div> -->
		<div id="main">
			<div id="header">
				<a id="logout" href="{url}/default/auth/logout{/url}">{t}Log Out{/t}</a>
				<h1 id="site-title1">{$site_name} - {t}Administration{/t}</h1>
				<ul class="topnav">
					{include file="file:$admin_theme_global_path/_nav_recursive.tpl" items=$nav_items.admin_header.children}
				</ul>
			</div>
			<div id="content">
				{include file="file:$admin_theme_global_path/_breadcrumbs.tpl"}
				{include file="file:$admin_theme_global_path/_messages.tpl"}
