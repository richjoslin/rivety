<?php

/*
	Class: RivetyCore_Acl

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		Zend_Acl
*/
class RivetyCore_Acl extends Zend_Acl
{

	/* Group: Constructors */

	/*
		Constructor: __construct

		Arguments:
			auth - A Zend_Auth object.
	*/
	public function __construct(Zend_Auth $auth)
	{
		// we need to do this recursively because of role inheritance
		$this->addRoles();

		$resources = new RolesResources();
		$rsResources = $resources->fetchAll();

		foreach ($rsResources as $resource)
		{
			$resource_mca = $resource->module . "-" . $resource->controller . "-" . $resource->action;
			if (!$this->has($resource_mca)) $this->add(new Zend_Acl_Resource($resource_mca));
			$this->allow($resource->role_id, $resource_mca);
		}
		$roles_res_extra_table = new RolesResourcesExtra();
		$res_extras = $roles_res_extra_table->fetchAll();
		if (count($res_extras) > 0)
		{
			foreach ($res_extras as $res_extra)
			{
				$extra_resource_mca = $res_extra->module . "-@@EXTRA-" . $res_extra->resource;
				if (!$this->has($extra_resource_mca)) $this->add(new Zend_Acl_Resource($extra_resource_mca));
				$this->allow($res_extra->role_id, $extra_resource_mca);
			}
		}
	}

	/* Group: Instance Methods */

	/*
		Function: addRoles

		Arguments:
			parent_role (optional) - TBD
	*/
	function addRoles($parent_role = null)
	{
		$roles_table = new Roles();

		// we start this recursive funtion by looking for roles with no parent.

		if (is_null($parent_role)) $roles = $roles_table->fetchParentless();
		else $roles = $roles_table->fetchImmediateChildren($parent_role);

		foreach ($roles as $role)
		{
			// Add the role and specifiy that as the parent. On the first pass, this is null.

			if (!$this->hasRole($role->id))
			{
				RivetyCore_Log::info("Adding role ".$role->shortname);
				$this->addRole(new Zend_Acl_Role($role->id), $parent_role);
			}
			if (count($roles_table->fetchImmediateChildren($role->id)) > 0)
			{
				$this->addRoles($role->id);
			}
		}
	}

}
