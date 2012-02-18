<?php
/*
	File: index.php
		Also known as the bootstrap.

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/

if (in_array("xdebug", get_loaded_extensions()))
{
	$xdebug_on = true;
	ini_set('display_errors', 1);
}
else
{
	$xdebug_on = false;
}

$basepath = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], "/"));

set_include_path(get_include_path() . PATH_SEPARATOR . $basepath);

$ip = $_SERVER['REMOTE_ADDR'];

try
{
	$is_cli = false;
	require_once ('header.php');

	if ($isInstalled)
	{
		if (!(boolean)$config['application']['launched'])
		{
			$allowed_ips = explode(",", $config['application']['allowed_ips']);
			$can_see = false;
			foreach ($allowed_ips as $allowed_ip)
			{
				if (strpos($ip, $allowed_ip) === 0) $can_see = true;
			}
			if (!$can_see)
			{
				header("Location: " . $config['application']['prelaunch_url']);
			}
		}
		if (RivetyCore_Registry::get('on_screen_errors') === '1')
		{
			ini_set('display_errors', 1);
			ERROR_REPORTING(E_ALL);
		}
	}

	$front = Zend_Controller_Front::getInstance();
	$front->setDefaultModule("default");
	$front->addModuleDirectory($RivetyCore_module_dir);
	$front->addModuleDirectory($module_dir);
	$front->throwExceptions(true);
	$front->setParam('noViewRenderer', false);

	// Replace the ViewRenderer with Smarty as the default view
	// args: template dir, smarty paths
	if ($isInstalled)
	{
		$smarty_config = new Zend_Config_Ini($config_file, 'smarty');
		$smarty_config_array = $smarty_config->config->toArray();
	}
	else
	{
		$smarty_view_compiles = $basepath . "/tmp/view_compiles";
		$smarty_cache_dir = $basepath . "/tmp/cache";
		if (!file_exists($smarty_view_compiles))
		{
			throw new Exception("DIR_MISSING Missing directory " . $smarty_view_compiles);
		}
		if (!file_exists($smarty_cache_dir))
		{
			throw new Exception("DIR_MISSING Missing directory " . $smarty_cache_dir);
		}
		if (!is_writable($smarty_view_compiles))
		{
			throw new Exception("CANT_WRITE Can't write to directory " . $smarty_view_compiles);
		}
		if (!is_writable($smarty_cache_dir))
		{
			throw new Exception("CANT_WRITE Can't write to directory " . $smarty_cache_dir);
		}
		$smarty_config_array['compile_dir'] = $smarty_view_compiles;
		$smarty_config_array['cache_dir'] = $smarty_cache_dir;
	}
	$smarty_config_array['plugins_dir'] = $smarty_plugins_dirs;
	Zend_Registry::set('smarty_config', $smarty_config_array);

	$view = new RivetyCore_View_Renderer(null, $smarty_config_array);
	$view_renderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
	$view_renderer
		->setNoController(true)
		->setViewBasePathSpec(RivetyCore_Registry::get('frontend_theme') . ':module/tpl_controllers/')
		->setViewScriptPathSpec(':controller/:action.:suffix')
		->setViewScriptPathNoControllerSpec(':action.:suffix')
		->setViewSuffix('tpl');
	Zend_Controller_Action_HelperBroker::addHelper($view_renderer);

	if ($isInstalled)
	{
		$front->registerPlugin(new AclPlugin);
		$params = array('front_controller' => $front);
		$RivetyCore_plugin->doAction('bootstrap', $params); // ACTION HOOK
	}
	else
	{
		$front->registerPlugin(new InstallPlugin);
	}

	$router = new Zend_Controller_Router_Rewrite();
	$front->setRouter($router);

	if ($isInstalled)
	{
		if (RivetyCore_Registry::get('enable_localization') == '1')
		{
			$router->addRoute('default', new Zend_Controller_Router_Route(":locale/:module/:controller/:action/*", array('locale' => '', 'module' => "default", 'controller' => "index", 'action' => "index",)));
		}
		else
		{
			$router->addRoute('default', new Zend_Controller_Router_Route(":module/:controller/:action/*", array('module' => "default", 'controller' => "index", 'action' => "index",)));
		}
		if (file_exists($routes_file))
		{
			$routes = new Zend_Config_Ini($routes_file, 'default');
			$router->addConfig($routes, 'routes');
		}
		$RivetyCore_plugin->doAction('bootstrap_routes', array('router' => $router)); // ACTION HOOK
	}

	// dd(explode(PATH_SEPARATOR, get_include_path()));
	// dd(get_loaded_extensions());

	$front->dispatch();
}
catch (Zend_Db_Statement_Exception $ex)
{
	if (canDebug($ip, $config))
	{
		d($ex->getMessage());
		dd($ex);
	}
	else
	{
		RivetyCore_Log::report("Database error", $ex, Zend_Log::EMERG);
		header("Location: /errordocuments/error_DB.html");
	}
}
catch (Exception $ex)
{
	if (!empty($config) && canDebug($ip, $config))
	{
		echo("<pre>");
		d($ex->getMessage());
		dd($ex);
		echo("</pre>");
	}
	else
	{
		$ex_type = trim(substr($ex->getMessage(), 0, strpos($ex->getMessage(), " ")));
		switch ($ex_type)
		{
		case "MISSING_LIBS":
			header("Location: /errordocuments/error_LIBS.html");
		break;
		case "CANT_WRITE":
			header("Location: /errordocuments/error_CANTWRITE.html");
		break;
		case "DIR_MISSING":
			header("Location: /errordocuments/error_DIRMISSING.html");
		break;
		default:
			if ($isInstalled)
			{
				header("Location: /errordocuments/500.html");
				RivetyCore_Log::report("Frontcontroller Error", $ex, Zend_Log::EMERG);
			}
			else
			{
				d($ex->getMessage());
				dd($ex);
			}
		break;
		}
	}
}
