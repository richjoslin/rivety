<?php

/*
	Class: RolesResources

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
	 	<RivetyCore_Db_Table_Abstract>
*/
class RolesResources extends RivetyCore_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
	protected $_name = 'default_roles_resources';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
	protected $_primary = array('role_id', 'module', 'controller', 'action');

	/* Group: Has No Methods */

	public function isAllowed($role_id,$module,$controller,$action){
		$out = false;
		$resource_db = $this->fetchRow($this->select()
			->where("role_id = ?", $role_id)
			->where("module = ?", $module)
			->where("controller = ?", $controller)
			->where("action = ?", $action)
		);
			
		if(!is_null($resource_db)){
			$out = true;
		}
		
		return $out;
	}


}
