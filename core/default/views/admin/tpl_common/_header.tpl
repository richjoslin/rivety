<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
		<title>{$title_prefix|escape} {if isset($pagetitle)}| {$pagetitle|escape}{/if}</title>
		<link type="text/css" rel="stylesheet" media="screen" href="{$admin_theme_url}/css/screen.css" />
		<link type="text/css" rel="stylesheet" media="screen" href="{$admin_theme_url}/css/jquery-ui/smoothness/jquery-ui-1.8.16.custom.css" />
		<link type="text/css" rel="stylesheet" media="screen" href="{$admin_theme_url}/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
		<link type="text/css" rel="stylesheet" media="screen" href="{$admin_theme_url}/css/jHtmlArea/jHtmlArea.css" media="screen" />
		{include_css urls=$css_urls}
		<script type="text/javascript">
			var username = "{$username}";
			var success = {if $success}true{else}false{/if};
		</script>
	</head>
	<body>
		<div id="switcher" style="position: absolute; top: 0; left: 500px;"></div>
		<div id="main">
			<div id="header">
				<div id="account-links">
					<ul>
						<li>
							{t}Welcome{/t}, <a href="{url}/default/user/profile/{$loggedInUsername}/{/url}" title="{$loggedInUsername}">{$loggedInUsername}</a>
						</li>
						<li>
							<a href="{url}/{/url}">{t}View Frontend{/t}</a>
						</li>
						<li>
							<a href="{url}/default/auth/logout{/url}">{t}Log Out{/t}</a>
						</li>
					</ul>
				</div>
				<h1 id="site-title1">{$site_name} - {t}Administration{/t}</h1>
				<ul class="topnav">
					{include file="file:$admin_theme_path/tpl_common/_nav_recursive.tpl" items=$nav_items.admin_header.children}
				</ul>
			</div>
			<div id="content">
				{include file="file:$admin_theme_path/tpl_common/_breadcrumbs.tpl"}
				{include file="file:$admin_theme_path/tpl_common/_screen_alerts.tpl"}
				<div id="page-loader">
					<img src="{$admin_theme_url}/img/loader-stripes.gif" width="220" height="19" alt="Loading..." />
				</div>
