<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
		<title>{$title_prefix|escape} {if isset($pagetitle)}| {$pagetitle|escape}{/if}</title>
		<link type="text/css" rel="stylesheet" media="screen" href="{$theme_url}/css/screen.css" />
		<link type="text/css" rel="stylesheet" media="screen" href="{$theme_url}/css/jquery-ui/smoothness/jquery-ui-1.8.16.custom.css" />
		{include_css urls=$css_urls}
	</head>
	<body>
		<div id="switcher" style="position: absolute; top: 0; left: 500px;"></div>
		<div id="main">
			<div id="header">
				<div id="account-links">
					{if $isLoggedIn}
						{if $isAdmin}
							<a href="{url}/default/admin/index{/url}" title="{t}Administration{/t}">{t}Admin{/t}</a>
						{/if}
						{t}Welcome{/t}, <a href="{url}/default/user/profile/username/{/url}{$loggedInUsername}" title="{$loggedInUsername}">{$loggedInFullName}</a>
						<a href="{url}/default/auth/logout{/url}" title="{t}Logout{/t}">{t}Logout{/t}</a>
					{else}
						<a href="{url}/default/auth/login{/url}" title="{t}Login{/t}">{t}Login{/t}</a>
						–
						<a href="{url}/default/user/register{/url}" title="{t}Register for an account{/t}">{t}Register{/t}</a>
					{/if}
				</div>
				<h1 id="site-title1">{$site_name}</h1>
				<ul class="topnav">
					{include file="file:$theme_path/tpl_common/_nav_recursive.tpl" items=$nav_items.main.children}
				</ul>
			</div>
			<div id="content">
				{include file="file:$theme_path/tpl_common/_breadcrumbs.tpl"}
				{include file="file:$theme_path/tpl_common/_messages.tpl"}
				<div id="page-loader">
					<img src="{$theme_url}/img/loader-stripes.gif" width="220" height="19" alt="Loading..." />
				</div>
