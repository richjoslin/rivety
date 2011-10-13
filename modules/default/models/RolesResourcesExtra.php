<?php

/*
	Class: RolesResourcesExtra

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
	 	<Cts_Db_Table_Abstract>
*/
class RolesResourcesExtra extends Cts_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
	protected $_name = 'default_roles_resources_extra';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
	protected $_primary = array('role_id', 'module', 'resource');

	/* Group: Has No Methods */
	
	
	public function isAllowed($role_id,$module,$resource){
		$out = false;
		$resource_db = $this->fetchRow($this->select()
			->where("role_id = ?", $role_id)
			->where("module = ?", $module)
			->where("resource = ?", $resource)
		);
			
		if(!is_null($resource_db)){
			$out = true;
		}
		
		return $out;
	}

}
