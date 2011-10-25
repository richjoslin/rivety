<?php

/*
	Class: Roles

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Db_Table_Abstract>
*/
class Roles extends RivetyCore_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
    protected $_name 	= 'default_roles';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
    protected $_primary = 'id';   

	/* Group: Has No Methods */
	
	/*
		Function: setInheritedRole
			Adds an inherited role to a role

		Arguments:
			id - role id to set inherited roles for
			inherited_id - role id to inherit
	*/
	function setInheritedRole($id,$inherited_id) {
		$roles_roles_table = new RolesRoles();
		$where = $roles_roles_table->getAdapter()->quoteInto("role_id = ?",$id);
		$where .= $roles_roles_table->getAdapter()->quoteInto(" and inherits_role_id = ?",$inherited_id);
		
		if($roles_roles_table->getCountByWhereClause($where) == 0){
			$data = array("role_id" => $id,"inherits_role_id" => $inherited_id);
			$roles_roles_table->insert($data);
		}
	}

	/*
		Function: removeInheritedRole
			Removes one or all inherited roles

		Arguments:
			id - role id to remove inherited roles for
			inherited_id - (optional) role id to stop inheriting. If this is null, all inherited roles are removed
	*/
	function removeInheritedRole($id,$inherited_id = null) {
		$roles_roles_table = new RolesRoles();
		$where = $roles_roles_table->getAdapter()->quoteInto("role_id = ?",$id);
		if(!is_null($inherited_id)){
			$where .= $roles_roles_table->getAdapter()->quoteInto("and inherits_role_id = ?",$inherited_id);
		}
		$roles_roles_table->delete($where);		
	}


	/*
		Function: getInheritedRoles
			Updates the metadata object for a user.

		Arguments:
			id - role id to fetch inherited roles for
						 
	*/
	function getInheritedRoles($id) {
		$roles_roles_table = new RolesRoles();
		$inherited_roles_db = $roles_roles_table->fetchAll($roles_roles_table->select()->where("role_id = ?", $id));
		$inherited_roles = array();
		if(count($inherited_roles_db) > 0){
			
			foreach($inherited_roles_db as $inherited_role){
				$inherited_roles[] = $inherited_role->inherits_role_id;
			}
		}
		return $inherited_roles;
	}
	
	function getAllAncestors($id){
		$parents = array();	
		if (is_array($id)) {
			$my_parents = array();
			foreach ($id as $i) {
				array_push($my_parents,$i);
			}
		} else {
			$my_parents = $this->getInheritedRoles($id);
		}

		foreach($my_parents as $my_parent){
			$parents[] = $my_parent;
			if(count($this->getInheritedRoles($my_parent)) > 0){
				$parents = array_merge($parents,$this->getAllAncestors($my_parent));
			}
			$parents = array_unique($parents);
		}
		return $parents;
		
	}

	function fetchImmediateChildren($id){
		$roles_roles_table = new RolesRoles();
		
		$child_roles = $roles_roles_table->fetchAll($roles_roles_table->select()->where("inherits_role_id = ?",$id));
		
		if(count($child_roles) > 0){
			$role_select = $this->select();
			foreach($child_roles as $child_role){
				$role_select->orWhere("id = ?",$child_role->role_id);
			}
			RivetyCore_Log::debug($role_select->assemble());
			return $this->fetchAll($role_select);
		}		
		   
	}

	


    function getRoleTree($parent_role = null,$dont_include = null) {
    	// we start this recursive funtion by looking for roles with no parent.
		$tree = null;
    	if (is_null($parent_role)) {
    		$roles = $this->fetchParentless();    		
    	} else {
    		$roles = $this->fetchImmediateChildren($parent_role);    		
    	}
    	

    	foreach ($roles as $role) {
			if($role->id != $dont_include){
				$tree[$role->id]['shortname'] = $role->shortname;			
				$children = $this->fetchImmediateChildren($role->id);
				
				if(count($children) > 0){
					$tree[$role->id]['children'] = $this->getRoleTree($role->id,$dont_include);
				}
			}			
    	}    	
    	return $tree;
    }	
	
	
	function fetchParentless(){
		$all_roles = $this->fetchAll();
		
		$select = $this->select();
		if(count($all_roles) > 0){
			foreach($all_roles as $role){
				if(count($this->getInheritedRoles($role->id)) == 0){					
					$select->orWhere("id = ?", $role->id);
				}
			}						
			return $this->fetchAll($select); 
		}
	}
	
	function fetchRolesByUsername($username){
		$users_roles_table = new UsersRoles();
		$roles_for_user = $users_roles_table->fetchAll($users_roles_table->select()->where("username = ?",$username));
		$select = $this->select();
		if(count($roles_for_user) > 0){
			foreach($roles_for_user as $role_for_user){
				$select->orWhere("id = ?",$role_for_user->role_id); 	
			}			 
		}
		
		return $this->fetchAll($select);
		
	}
	
	function getRoleIdsByUsername($username){		
		$roles = $this->fetchRolesByUsername($username);
		$out = array();
		if(count($roles) > 0){
			foreach($roles as $role){
				$out[] = $role->id;
			}
		}
		return $out;		
	}

	function getRoleShortnamesByUsername($username) {
		$roles = $this->fetchRolesByUsername($username);
		$out = array();
		if (count($roles) > 0) {
			foreach ($roles as $role) {
				$out[] = $role->shortname;
			}
		}
		return $out;
	}
	
	function getIdByShortname($shortname){
		$id = null;		
		$role = $this->fetchRow($this->select()->where("shortname = ?",$shortname));
		if(!is_null($role)){
			$id = $role->id;
		}
		return $id;		
	}	
	function getShortnameById($id){
		$shortname = null;		
		$role = $this->fetchRow($this->select()->where("id = ?",$id));
		if(!is_null($role)){
			$shortname = $role->shortname;
		}
		return $shortname;		
	}	

}
