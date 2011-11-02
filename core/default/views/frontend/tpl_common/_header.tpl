<!DOCTYPE html>
<html>
	<head>
		<title>{$title_prefix|escape} {if isset($pagetitle)}| {$pagetitle|escape}{/if}</title>
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" media="screen" href="{$default_theme_url}/css/screen.css" />
		{include_css urls=$css_urls}
	</head>
	<body>
		<div id="main">
			<div id="header">
				<h1><a href="/" title="">Managed Website</a></h1>
				<div id="nav">
					<ul>
						{foreach from=$nav_items.main.children item=link key=index}
							<li>
								<a href="{if isset($link.url)}{$link.url}{else}#{/if}">{$link.link_text}</a>
							</li>
						{/foreach}
					</ul>
				</div>
				<div id="status">
					{if $isLoggedIn}
						{if $isAdmin}
							<a href="{url}/default/admin/index{/url}" title="{t}Administration{/t}">{t}Admin{/t}</a>
						{/if}
						{t}Welcome{/t}, <a href="{url}/default/user/profile/username/{/url}{$loggedInUsername}" title="{$loggedInUsername}">{$loggedInFullName}</a>
						<a href="{url}/default/auth/logout{/url}" title="{t}Logout{/t}">{t}Logout{/t}</a>
					{else}
						<a href="{url}/default/auth/login{/url}" title="{t}Login{/t}">{t}Login{/t}</a>
						{*<a href="{url}/default/user/register{/url}" title="{t}Register for an account{/t}">{t}Register{/t}</a>*}
					{/if}
				</div>
			</div>
			<div id="content">
