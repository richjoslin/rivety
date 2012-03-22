<?php
/*
	File: header.php
		This file is included globally.

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/
error_reporting(E_ALL | E_STRICT);
/* Group: Global Methods */
/*
	Function: __autoload
		Automatically loads a class. Tries several techniques, including require_once() and Zend_Loader::loadClass().
		If it fails to load the class using the attempted methods, an error is displayed and the Response is ended.

	Arguments:
		class_name - The name of the class to load (not a filename).
*/
function autoload($class_name)
{
	try
	{
		if ($class_name == "Smarty")
		{
			require_once $class_name . '.class.php';
		}
		else
		{
			try
			{
				Zend_Loader::loadClass($class_name);
			}
			catch(Zend_Exception $ex)
			{
				$places_to_look = explode(PATH_SEPARATOR, get_include_path());
				$found = false;
				foreach ($places_to_look as $place_to_look)
				{
					$filenames = array(
						$place_to_look . DIRECTORY_SEPARATOR . $class_name . ".php",
						$place_to_look . DIRECTORY_SEPARATOR . $class_name . ".inc.php"
					);
					foreach($filenames as $filename){
					
						if (file_exists($filename))
						{
							require_once $filename;
							$found = true;
							break;
						}
						
						if($found){
							break;
						}
					}					
					
				}
				
				if (!$found)
				{
					throw (new Exception("Can't find a class called '" . $class_name . "'. Here's a backtrace: " . print_r(debug_backtrace(), true)));
				}
				
			}
		}
	}
	catch(Exception $e)
	{
		dd($e);
	}
}

function pearLoader($classname){
	 // this is to take care of the PEAR style of naming classes
    $path = str_ireplace('_', '/', $classname);
    if(@include_once $path.'.php'){
        return;
    }
}

spl_autoload_register("autoload");
spl_autoload_register("pearLoader");

/*
	Function: d
		Dump the contents of a variable into the HTTP Response. Stands for "dump".

	Arguments:
		a_var - The variable to dump into the Response.

	See Also:
		- Zend_Debug::dump()
*/
function d($a_var)
{
	global $xdebug_on;
	if (!$xdebug_on)
	{
		echo ("<pre>");
		ob_start();
		var_dump($a_var);
		$a = ob_get_contents();
		ob_end_clean();
		echo htmlspecialchars($a, ENT_QUOTES);
		echo ("</pre>");
	}
	else
	{
		var_dump($a_var);
	}
}
/*
	Function: dd
		Dump the contents of a variable into the HTTP Response and then end the HTTP Response. Stands for "dump and die".

		Arguments:
			a_var - The variable to dump into the Response.

		See Also:
			- Zend_Debug::dump()
			- die()
*/
function dd($a_var,$for_web=false)
{
	if($for_web) echo("<pre>");	
	d($a_var);
	if($for_web) echo("</pre>");
	die();
}
/*
	Function: var_name
*/
function var_name(&$var, $scope = false, $prefix = 'unique', $suffix = 'value')
{
	if ($scope) $vals = $scope;
	else $vals = $GLOBALS;
	$old = $var;
	$var = $new = $prefix . rand() . $suffix;
	$vname = FALSE;
	foreach ($vals as $key => $val)
	{
		if ($val === $new) $vname = $key;
	}
	$var = $old;
	return $vname;
}
/*
	Function: canDebug
		Read the debug_ips param from the config and determine if the passed ip is one of them
*/
function canDebug($ip, $config)
{
	if (array_key_exists("debug_ips", $config['application'])) $debug_ips = explode(",", $config['application']['debug_ips']);
	else $debug_ips = array();
	$debug_ips[] = "127.0.0.1";
	if (in_array($ip, $debug_ips)) return true;
	else return false;
}
if(empty($basepath)){
	$basepath = dirname(__FILE__);
}
$config_file = $basepath . '/etc/config.ini';
$routes_file = $basepath . '/etc/routes.ini';
$isInstalled = false;
if (file_exists($config_file))
{
	
	$isInstalled = true;
	$config = parse_ini_file($config_file, true);
	date_default_timezone_set("{$config['application']['timezone']}");
	$zf_path = $config['application']['zf_path'];
	$smarty_path = $config['application']['smarty_path'];
	$asido_path = $config['application']['asido_path'];
	$salt = $config['application']['salt'];
}
else
{
	date_default_timezone_set("America/Los_Angeles");
	$zf_path = $basepath . "/lib/ZendFramework/library";
	$smarty_path = $basepath . "/lib/Smarty/libs";
	$asido_path = $basepath . "/lib/Asido";
}
if (!file_exists($zf_path . "/Zend/Loader.php"))
{
	throw new Exception("MISSING_LIBS - Can't find Zend Framework in " . $zf_path);
}
if (!file_exists($smarty_path . "/Smarty.class.php"))
{
	throw new Exception("MISSING_LIBS - Can't find Smarty Template Engine in " . $smarty_path);
}
if (!file_exists($asido_path . "/class.asido.php"))
{
	throw new Exception("MISSING_LIBS - Can't find Asido in " . $asido_path);
}
set_include_path(get_include_path() . PATH_SEPARATOR . $zf_path);
set_include_path(get_include_path() . PATH_SEPARATOR . $smarty_path);
set_include_path(get_include_path() . PATH_SEPARATOR . $asido_path);
require_once ('Zend/Loader.php');

Zend_Registry::set('basepath', $basepath);
if(!empty($salt)){
	Zend_Registry::set('password_salt', $salt);	
}


$tmp_inculde_path = "";
$RivetyCore_module_dir = $basepath . "/core";
$module_dir = $basepath . "/modules";
$rivety_dir = $RivetyCore_module_dir . "/default";
$subdirs = array("models", "plugins", "controllers", "lib");
// set include paths for default module
foreach ($subdirs as $subdir)
{
	set_include_path(get_include_path() . PATH_SEPARATOR . $rivety_dir . DIRECTORY_SEPARATOR . $subdir);
}
// set up default smarty plugins dir as a string
$smarty_plugins_dirs = $rivety_dir . DIRECTORY_SEPARATOR . 'smarty_plugins';
// define constants
$constants = new Constants();

if ($isInstalled)
{
	$log_level = null;

	if (is_null(@$config['application']['log_level'])) $log_level = Zend_Log::DEBUG;
	else $log_level = (int)$config['application']['log_level'];

	if (is_null(@$config['application']['host_id'])) $host_id = null;
	else $host_id = $config['application']['host_id'];

	if(empty($is_cli))$is_cli=false;

	if ($is_cli) $log_filename = $config['application']['log_filename_cli'];
	else $log_filename = $config['application']['log_filename'];

	Zend_Registry::set('basepath', $basepath);
	Zend_Registry::set('config_file', $config_file);
	Zend_Registry::set('host_id', $host_id);

	// create logger
	$writer = new Zend_Log_Writer_Stream($log_filename);
	$filter = new Zend_Log_Filter_Priority($log_level);
	$writer->addFilter($filter);
	RivetyCore_Log::registerLogger('rivety', $writer, true);
	RivetyCore_Log::report("Log Started", null, Zend_Log::INFO);

	// Create Plugin Manager
	$RivetyCore_plugin = RivetyCore_Plugin::getInstance();


	set_include_path(get_include_path() . PATH_SEPARATOR . $config['application']['addtl_includes']);
	$databases = new Zend_Config_Ini($config_file, 'databases');
	$dbAdapters = array();
	foreach ($databases->db as $config_name => $db)
	{
		$dbAdapters[$config_name] = Zend_Db::factory($db->adapter, $db->config->toArray());
		if ((boolean)$db->config->default)
		{
			Zend_Db_Table::setDefaultAdapter($dbAdapters[$config_name]);
		}
	}
	// Store the adapter for use anywhere in our app
	$registry = Zend_Registry::getInstance();
	$registry->set('dbAdapters', $dbAdapters);
	// check for database changes
	$modules_table = new Modules('core');
	$modules_table->upgradeDatabase("default");
	$modules_table->setDefaultConfig("default");
	$config_table = new Config();
	$config_array = $config_table->fetchall()->toArray();
	foreach ($config_array as $config_param)
	{
		Zend_Registry::set($config_param['ckey'], $config_param['value']);
	}
	// // Make session use the DB
	// Zend_Session::setSaveHandler(new RivetyCore_SessionSaveHandler());
	// Zend_Session::start();
	// Get the list of modules from the db
	$modules_table = new Modules('modules');
	$enabled_modules = $modules_table->fetchAll("is_enabled = 1");
	if (count($enabled_modules) > 0)
	{
		foreach ($enabled_modules as $module)
		{
			$full_dir = $module_dir . "/" . $module->id;
			if ($modules_table->isEnabled($module->id))
			{
				$modules_table->setup($module->id);
				$smarty_plugins_dir = $full_dir . '/smarty_plugins';
				// if there are any OTHER smarty plugin dirs in other modules, convert smarty_plugin_dirs to an array and add the default
				if (is_dir($smarty_plugins_dir))
				{
					$tmp_dir = $smarty_plugins_dirs;
					if (is_array($smarty_plugins_dirs))
					{
						$smarty_plugins_dirs[] = $smarty_plugins_dir;
					}
					else
					{
						$smarty_plugins_dirs = array($tmp_dir, $smarty_plugins_dir);
					}
				}
			}
		}
	}
}
