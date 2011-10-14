<?php

/*
	Class: Resource

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>
		<RivetyCore_Controller_Action_Admin>
*/
class ResourceController extends RivetyCore_Controller_Action_Admin {

	/* Group: Instance Methods */

	/*
		Function: init
		Invoked automatically when an instance is created.
		Initializes the current instance.
		Initializes the parent object (calls init() on the parent instance).
	*/
	function init() {
		parent::init();
	}

	/* Group: Actions */

	/*
		Function: edit
			Edit which resources a role has access to by module.

		HTTP GET or POST Parameters:
			id - role id to edit resources for (id, not shortname)
			modid - module id of module to edit resources for. defaults to 'default'

		View Variables:
			actions - An array of all available actions in the app.
			pagetitle - The HTML page title.
			role - array containing the role information for the chosen role
			modid - id of the current module
			module_title - Name of the current module, pulled from module.ini
			enabled_modules - An array of modules that are currently enabled
	*/
	function editAction()
	{

		$request = new RivetyCore_Request($this->getRequest());

		$modules_table = new Modules('modules');
		$modules_table_core = new Modules('default');

		$roles_resources_table = new RolesResources();
		$roles_res_extra_table = new RolesResourcesExtra();

		if ($request->has("id"))
		{
			$role_id = $request->id;
			$roles_table = new Roles();
			$role = $roles_table->fetchRow("id = ".$role_id);
			if (!is_null($role))
			{
				$this->view->role = $role->toArray();
				$this->view->roleshortname = $role->shortname;
			}
			else
			{
				$this->_redirect("/role");
			}
		}
		else
		{
			$this->_redirect("/role");
		}

		if ($request->has("modid"))
		{
			if ($modules_table->exists($request->modid))
			{
				$module_id = $request->modid;
			}
			else
			{
				$module_id = "default";
			}
		}
		else
		{
			$module_id = "default";
		}

		if ($this->getRequest()->isPost())
		{

			$resources = $this->getRequest()->getPost('resource');

			// Hose everything for this role and module
			$where = $roles_resources_table->getAdapter()->quoteInto("role_id = ? and ", $role_id);
			$where .= $roles_resources_table->getAdapter()->quoteInto("module = ? ", $module_id);
			$roles_resources_table->delete($where);

			foreach ($resources as $resource)
			{
				$resource_array = explode("-", $resource);
				$resource_module = $resource_array[0];
				$resource_controller = $resource_array[1];
				$resource_action = $resource_array[2];
				$data = array(
					'role_id' => $role_id,
					'module' => $resource_module,
					'controller' => $resource_controller,
					'action' => $resource_action,
				);
				$roles_resources_table->insert($data);
			}

			$where = $roles_res_extra_table->getAdapter()->quoteInto("role_id = ? and ", $role_id);
			$where .= $roles_res_extra_table->getAdapter()->quoteInto("module = ? ", $module_id);
			$roles_res_extra_table->delete($where);

			if ($request->has("extra_resource"))
			{
				foreach ($request->extra_resource as $extra_resource_item)
				{
					$data = array(
						'role_id' => $role_id,
						'module'  => $module_id,
						'resource'=> $extra_resource_item,
					);
					$roles_res_extra_table->insert($data);
				}
			}
			$this->view->success = $this->_T("Resources updated.");
		}

		$db_roles_resources = $roles_resources_table->fetchAll('role_id = ' . $role_id );

		$resources = array();

		foreach ($db_roles_resources as $resource) {
			if (!array_key_exists($resource->module, $resources)) {
				$resources[$resource->module] = array();
			}
			if (!array_key_exists($resource->controller, $resources[$resource->module])) {
				$resources[$resource->module][$resource->controller] = array();
			}
			$resources[$resource->module][$resource->controller][] = $resource->action;
		}

		/*
		* This is a poor man's introspector. The reflection API needs the classes actually available,
		* which creates naming conflicts between modules. What I do instead is read the physical files,
		* line by line, find the lines with "function fooAction" and determine that the action name is
		* "foo". It's a hack, but it works.
		*/

		$all_actions = array();
		$modules = array();
		$controllerdirs = array();

		$enabled_modules = $modules_table->getEnabledModules();

		foreach ($enabled_modules as $enabled_module)
		{
			$module_dir = 'modules';
			if ($enabled_module == 'default') $module_dir = 'core';
			$controllerdirs[$enabled_module] = Zend_Registry::get("basepath") . DIRECTORY_SEPARATOR . $module_dir . DIRECTORY_SEPARATOR . $enabled_module . DIRECTORY_SEPARATOR . "controllers";
		}

		$controllerdir = $controllerdirs[$module_id];

		$d = dir($controllerdir);
		$modules[] = $module_id;

		while (($entry = $d->read()) !== false)
		{
			if ($entry != '.' and $entry != '..' and $entry != '.svn')
			{
				$controller_name = substr($entry, 0, stripos($entry, 'Controller.php'));
				if ($module_id != "default" && substr($controller_name, 0, 1) == "_")
				{
					$controller_name = substr($controller_name, stripos($controller_name, '_') + 1);
				}
				$lines = file($controllerdir.'/'.$entry);
				foreach ($lines as $line)
				{
					if (preg_match('/function.*Action.*\(.*\).*\{?/', $line))
					{
						$action_name = trim(preg_replace('/Action.*/', '', preg_replace('/^.*function/', '', $line)));

						$allowed = false;
						if (array_key_exists($module_id, $resources))
						{
							if (array_key_exists($controller_name, $resources[$module_id]))
							{
								if (in_array($action_name, $resources[$module_id][$controller_name]))
								{
									$allowed = true;
								}
							}
						}
						$inherited = false;
						if (count($roles_table->getInheritedRoles($role_id)) > 0)
						{
							$inherited = $this->isResourceInherited($module_id, $controller_name, $action_name, $role_id);
						}
						$all_actions[$module_id][$controller_name][$action_name] = array(
							'allowed' => $allowed,
							'inherited' => $inherited,
						);
					}
				}
			}
		}

		$d->close();
		$this->view->modid = $module_id;

		// TODO: change to rivety
		if ($module_id == 'default') $mod_cfg = $modules_table_core->parseIni($module_id);
		else $mod_cfg = $modules_table->parseIni($module_id);

		$this->view->module_title = $mod_cfg['general']['name'];
		$this->view->actions = $all_actions;
		$this->view->modules = $enabled_modules;

		// get "extra" resources
		$extra_resources = array();
		if (array_key_exists('resources', $mod_cfg))
		{
			foreach ($mod_cfg['resources'] as $resource_name => $nicename)
			{
				$extra_resources[$resource_name]['nicename'] = $nicename;
				$extra_resources[$resource_name]['inherited'] = $this->isExtraResourceInherited($module_id, $resource_name, $role_id);
				$extra_resources[$resource_name]['allowed'] = $roles_res_extra_table->isAllowed($role_id, $module_id, $resource_name);
			}
		}
		$this->view->extra_resources = $extra_resources;
	}

	/* Group: Private or Protected Methods */

	/*
		Function: isResourceInherited
			Used by <edit> to determine if the permissions on a resource are inherited from another role. This function is recursive.

		Arguments:
			$module - The name of the module for the resource in question.
			$controller - The name of the controller for the resource in question.
			$action - The name of the action for the resource in question.
			$role_id - The ID of the role in question.

		Returns:
			A boolean indicating whether the permissions on the indicated resource are inherited from another role.
	*/
	protected function isResourceInherited($module, $controller, $action, $role_id) {
		$inheritsResource = false;
		$roles_table = new Roles();
		$roles_roles_table = new RolesRoles();
		$roles_resources_table = new RolesResources();
		$inherited_ids = $roles_table->getAllAncestors($role_id);
		if (count($inherited_ids) > 0) {
			foreach ($inherited_ids as $inherited_id) {
				// determine if parent has access to this resource
				$roles_resource = $roles_resources_table->fetchRow(
					"role_id=".$inherited_id." and ".
					"module='".$module."' and ".
					"controller='".$controller."' and ".
					"action='".$action."' "
				);
				if (!is_null($roles_resource)) {
					//parent has it, role is inherited
					$inheritsResource = true;
				}
			}
		}
		return $inheritsResource;
	}

	/*
		Function: isExtraResourceInherited
			Used by <edit> to determine if the permissions on a resource are inherited from another role. This function is recursive.

		Arguments:
			$module - The name of the module for the resource in question.
			$resource - The name of the resource in question.
			$role_id - The ID of the role in question.

		Returns:
			A boolean indicating whether the permissions on the indicated resource are inherited from another role.
	*/
	protected function isExtraResourceInherited($module, $resource, $role_id) {
		$inheritsResource = false;
		$roles_table = new Roles();
		$roles_roles_table = new RolesRoles();
		$roles_res_extra_table = new RolesResourcesExtra();
		$inherited_ids = $roles_table->getAllAncestors($role_id);
		if (count($inherited_ids) > 0) {
			foreach ($inherited_ids as $inherited_id) {
				// determine if parent has access to this resource
				$select = $roles_res_extra_table->select();
				$select->where("role_id = ?", $inherited_id);
				$select->where("module = ?", $module);
				$select->where("resource = ?", $resource);
				$roles_resource = $roles_res_extra_table->fetchRow($select);
				if (!is_null($roles_resource)) {
					//parent has it, role is inherited
					$inheritsResource = true;
				}
			}
		}
		return $inheritsResource;
	}

}
