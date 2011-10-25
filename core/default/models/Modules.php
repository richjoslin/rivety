<?php

/*
	Class: Modules

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
	 	<RivetyCore_Db_Table_Abstract>
*/
class Modules extends RivetyCore_Db_Table_Abstract
{

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
	protected $_name = 'default_modules';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
	protected $_primary = 'id';

	/*
		Variable: $_parsed_ini
	*/
	protected $_parsed_ini = array();

	/*
		Variable: $errors
	*/
	public $errors = null;

	/*
		Variable: $success
	*/
	public $success = null;

	/*
		Variable: $notice
	*/
	public $notice = null;

	protected $_cache;
	public $module_dir;

	function __construct($module_dir)
	{
		if (is_null($module_dir))
		{
			throw new Exception('Module dir cannot be null.');
		}
		$this->module_dir = $module_dir;
		parent::__construct();
	}

	/* Group: Instance Methods */

	/*
		Function: exists
			Verifies the existence of a module.
			This method does not check whether the module is installed or enabled.
			To check whether a module is enabled, use <isEnabled>.

		Arguments:
			module_id - The ID of the module for which to verify its existence.

		Returns: boolean

		About: See Also
			- <isEnabled>
	*/
	function exists($module_id)
	{
		$where = $this->getAdapter()->quoteInto("id = ?", $module_id);
		if ($this->getCountByWhereClause($where) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
		Function: getEnabledModules
			Returns a list of modules which are set as enabled in the database.

		Arguments:
			include_default - A boolean to indicate if the default module should be included in the returned array. Default is true.

		Returns: array
	*/
	function getEnabledModules($include_default = true)
	{
		$enabled_modules = array();
		$modules = $this->fetchAll("is_enabled = 1");
		if (count($modules) > 0)
		{
			foreach ($modules as $module)
			{
				$enabled_modules[] = $module->id;
			}
		}
		if ($include_default)
		{
			$enabled_modules[] = "default";
		}
		return $enabled_modules;
	}

	/*
		Function: isEnabled
			Verifies the enabled status of a module.
			Does not necessarily verify whether a module exists or is installed,
			but inherently, if a module comes up positive for being enabled,
			then it does exist and has been installed.

		Arguments:
			module_id - The ID of the module for which to verify its enabled status.

		Returns: boolean
	*/
	function isEnabled($module_id) {
		$where = $this->getAdapter()->quoteInto("id = ? and is_enabled = 1", $module_id);
		if ($this->getCountByWhereClause($where) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/*
		Function: isInstalled
			Verifies the installed status of a module.
			Does not necessarily verify whether a module exists,
			but inherently, if a module comes up positive for being installed,
			then it does exist.

		Arguments:
			module_id - The ID of the module for which to verify its installed status.

		Returns: boolean
	*/
	function isInstalled($module_id) {
		$where = $this->getAdapter()->quoteInto("id = ?", $module_id);
		if ($this->getCountByWhereClause($where) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/*
		Function: enable
			Enables a module.

		Arguments:
			module_id - The ID of the module to enable.

		Returns: boolean indicating whether the filter was successfully executed
	*/
	function enable($module_id) {
		if ($this->runFilter($module_id."_enable")) {
			$where = $this->getAdapter()->quoteInto("id = ?", $module_id);
			$data['is_enabled'] = 1;
			$this->update($data, $where);
			return true;
		} else {
			return false;
		}
	}

	/*
		Function: disable

		Arguments:
			The ID of the module to disable.

		Returns: boolean indicating whether the filter was successfully executed
	*/
	function disable($module_id) {
		if ($this->runFilter($module_id."_disable")) {
			$where = $this->getAdapter()->quoteInto("id = ?", $module_id);
			$data['is_enabled'] = 0;
			$this->update($data, $where);
			return true;
		} else {
			return false;
		}
	}

	/*
		Function: install
			Installs a module. The module files must be placed in the modules directory for it to be installable.
			Installation should typically be done using the modules screen in the admin tools.

		Arguments:
			module_id - The ID of the module to install.

		Returns: boolean indicating whether the filter was successfully executed
	*/
	function install($module_id) {
		$this->setup($module_id);
		if ($this->runFilter($module_id."_install")) {
			$data = array("id" => $module_id);
			$this->insert($data);
			return true;
		} else {
			return false;
		}
	}

	/*
		Function: uninstall
			Uninstalls a module.

		Arguments:
			module_id - The ID of the module to uninstall.

		Returns: boolean indicating whether the filter was successfully executed
	*/
	function uninstall($module_id) {
		$this->setup($module_id);
		if ($this->runFilter($module_id."_uninstall")) {
			$where = $this->getAdapter()->quoteInto("id = ?", $module_id);
			$this->delete($where);
			return true;
		} else {
			return false;
		}
	}

	/*
		Function: parseIni
			Parses an module.ini file for a given module.

		Arguments:
			module_id - The ID of the module for which to parse the INI file.

		Returns: an array representing the parsed config file
	*/
	function parseIni($module_id)
	{
		$config = null;

		// first, try to load it internally
		if (array_key_exists($module_id,$this->_parsed_ini))
		{
			$config = $this->_parsed_ini[$module_id];

		}

		// if we're still null, see if we have a cache and load it from there.

		if (is_null($config))
		{
			if (isset($this->_cache))
			{
				$cacheKey = $module_id . "_cfg";
				$config = $this->_cache->load($cacheKey);
			}
		}

		// if we're STILL null, read the file.

		if (empty($config))
		{
			$basepath = Zend_Registry::get("basepath");
			// $module_ini_file = $basepath . "/modules/" . $module_id . "/module.ini";
			$module_ini_file = $basepath . DIRECTORY_SEPARATOR . $this->module_dir . DIRECTORY_SEPARATOR . $module_id . DIRECTORY_SEPARATOR . "module.ini";

			if (file_exists($module_ini_file))
			{
				$config = parse_ini_file($module_ini_file, true);
				$this->_parsed_ini[$module_id] = $config;
			}
			if (isset($this->_cache))
			{
				$this->_cache->save($config,$cacheKey);
			}
		}

		// if we're null now, something bad happened, throw an exception.

		if (!is_null($config))
		{
			return $config;
		}
		else
		{
			throw new Exception("Can't find module.ini for ".$module_id);
		}
	}

	/*
		Function: setDefaultConfig

		Arguments:
			module_id - TBD

		Returns: void
	*/
	function setDefaultConfig($module_id)
	{
		try
		{
			$module_cfg = $this->parseIni($module_id);
			$config_table = new Config();
			if (array_key_exists('settings', $module_cfg))
			{
				foreach ($module_cfg['settings'] as $key => $value)
				{
					if (!$config_table->keyExists($module_id, $key))
					{
						$config_table->set($module_id, $key, $value);
					}
				}
			}
		} catch (Exception $e){
			RivetyCore_Log::report($e->getMessage(),$e, Zend_Log::ERR);
		}
	}

	/*
		Function: upgradeDatabase
			Upgrades the database components for a given module to the latest version for that module.

		Arguments:
			module_id - The ID of the module for which to upgrade the database to the latest version.

		Returns: void
	*/
	function upgradeDatabase($module_id) {

		$db_versions_table = new DatabaseVersions();
		$module_cfg = $this->parseIni($module_id);
		$required_versions = $module_cfg['database_versions'];

		foreach ($required_versions as $r_module_id => $required_version) {
			$required_version = (int)$required_version;
			$current_version = (int)$db_versions_table->getCurrentVersion($r_module_id);

			if ($current_version < $required_version) {
				// database for this module is out of date.  get scripts to run.
				$script = new RivetyCore_Db_Script();
				$change_scripts = array();
				$all_scripts = $script->getScripts($module_id);
				foreach ($all_scripts as $script) {
					if (strpos($script,"change_") !== false) {
						$change_num = (int)substr($script, strlen("change_"));
						$change_scripts[$change_num] = $script;
					}
				}
				ksort($change_scripts);

				foreach ($change_scripts as $num => $script_name) {
					if ($num > $current_version and $num <= $required_version) {

						$change_script = new RivetyCore_Db_Script($module_id, $script_name);

						if ($change_script === false) {
							$error_string = "CANT_UPGRADE - Script ".$script_name." for module ".$module_id ." failed. Errors were: ";
							foreach ($change_script->errors as $error) {
								$error_string .= $error." || ";
							}
							throw new Exception($error_string);
						} else {
							$db_versions_table->setCurrentVersion($module_id, $num);
							// UPDATE ALL VIEWS
							// TODO - check to see if the view file exists
							$views_script = new RivetyCore_Db_Script($module_id, 'views');
							// TODO - report errors if the view script exists and fails to run
						}
					}
				}
			}
		}
	}

	/*
		Function: setup

		Arguments:
			module_id - The ID of the module for which to set up.

		Returns: void
	*/
	function setup($module_id) {
		$basepath = Zend_Registry::get("basepath");
		$module_dir = $basepath."/modules";
		$full_dir = $module_dir."/".$module_id;
		$subdirs = array("models", "plugins", "controllers", "lib");
		$tmp_include_path = "";
		try{
			$module_cfg = $this->parseIni($module_id);

			if (is_dir($full_dir)) {
			   	foreach ($subdirs as $subdir) {
					$includable_dir = $full_dir."/".$subdir;
					if (is_dir($includable_dir)) {
						$tmp_include_path .= PATH_SEPARATOR.$includable_dir;
					}
				}
				set_include_path(get_include_path().$tmp_include_path);
			}

			$this->upgradeDatabase($module_id);
			$this->setDefaultConfig($module_id);

			$ap = RivetyCore_Plugin::getInstance();

			if (count($module_cfg['plugins']) > 0) {
				foreach ($module_cfg['plugins'] as $hook => $plugin) {
					$hook_type = substr($hook, 0, strpos($hook, "."));
					$hook_name = substr($hook, strpos($hook, ".") + 1);
					$callback_class = substr($plugin, 0, strpos($plugin, "::"));
					$callback_method = substr($plugin, strpos($plugin, "::") + 2);
					if ($hook_type == "filter") {
						$ap->addFilter($hook_name, $callback_class, $callback_method, 10);
					}
					if ($hook_type == "action") {
						$ap->addAction($hook_name, $callback_class, $callback_method, 10);
					}
				}
			}
		} catch (Exception $e) {
			RivetyCore_Log::report("Could not set up ".$module_id, $e, Zend_Log::ERR);
			// $where = $this->getAdapter()->quoteInto("id = ?", $module_id);
			// $this->delete($where);
		}
	}

	/* Group: Private or Protected Methods */

	/*
		Function: runFilter
			Executes a filter.

		Arguments:
			filter_name - The name of the filter to execute.

		Returns: boolean
	*/
	private function runFilter($filter_name) {
		$params = array(
			"errors" => array(),
			"success" => null, // TODO : clarify data types in this array
			"notice" => null,
		);
		$params = $this->_rivety_plugin->doFilter($filter_name, $params);
		$this->success = $params['success'];
		$this->notice = $params['notice'];
		$this->errors = $params['errors'];
		if (count($params['errors']) > 0) {
			return false;
		} else {
			return true;
		}
	}

}
