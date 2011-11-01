<?php

/*
	Class: Config

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>
		<RivetyCore_Controller_Action_Admin>
*/
class ConfigController extends RivetyCore_Controller_Action_Admin
{

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			Initializes the current instance.
			Also initializes the parent object (calls init() on the parent instance).
	*/
	function init()
	{
		Zend_Loader::loadClass('Config');
		parent::init();
	}

	/* Group: Actions */

	/*
		Function: index

		HTTP GET or POST Parameters:
			modid - module id of module to edit resources for. defaults to 'default'

		Plugin Hooks:
			- *config_index_post_save* (action) - Allows you to perform actions after the configuration values are saved to the database. No params.

		View Variables:
			config - An array of all configurable settings for the current module
			pagetitle - The HTML title of the page.
			success - A string containing a message of success to be displayed on the page upon successful configuration update.
			modules - an array of enabled module ids
			current - the module id of the module currently being shown
			modid	- the module id of the module currently being shown
	*/
	function indexAction()
	{
		$config_table = new Config();
		$modules_table = new Modules('core');
		$request = new RivetyCore_Request($this->getRequest());
		$modid = $request->has('modid') ? $request->modid : 'default';
		if ($this->_request->isPost())
		{
			$config_params = $this->_request->getParams();
			foreach ($config_params as $ckey => $value)
			{
				$data = array('value' => $value);
				$config_table->update($data, "ckey = '" . $ckey . "' and module = '" . $modid . "'");
			}
			$this->view->success = $this->_T('Configuration Updated.');
			$config_table->cache();
			$params = array();
			$this->_rivety_plugin->doAction($this->_mca . '_post_save', $params); // ACTION HOOK
		}
		$config = $config_table->fetchAll($config_table->select()->where('module = ?', $modid));
		if (count($config) > 0)
		{
			$config = $config->toArray();
			sort($config);
			$this->view->config = $config;
		}
		$modules = $modules_table->getEnabledModules();
		sort($modules);
		$this->view->modules = $modules;
		$this->view->current = $modid;
		$this->view->modid = $modid;

		$this->view->breadcrumbs = array(array('text' => 'Core Config'));
	}

}
