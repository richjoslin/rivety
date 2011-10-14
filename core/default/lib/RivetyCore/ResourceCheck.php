<?php

class RivetyCore_ResourceCheck{
	
	static function isAllowed($resource,$module = "default",$username = null,$controller = null){
		$users_roles_table = new UsersRoles();
		$roles_table = new Roles();
		if(!is_null($username)){
			$users_roles_db = $users_roles_table->fetchAll($users_roles_table->select()->where("username = ?",$username));
			
			if(count($users_roles_db) > 0){
				$user_roles = array();
				$users_roles_db = $users_roles_db->toArray();
				
				foreach($users_roles_db as $role){
					$ancs = $roles_table->getAllAncestors($role['role_id']);
					
					foreach ($ancs as $anc => $value) {
						$user_roles[] = $value;
					}
					
					array_push($user_roles, $role['role_id']);
				}
				$user_roles = array_unique($user_roles);
			}
		} else {
			$user_roles = array($roles_table->getIdByShortname("guest"));			
		}
		if (is_null($controller)) {
			$controller = "@@EXTRA";
		} 
		$resource_name = $module ."-". $controller ."-". $resource;
		$out = array();
		
		
		if(Zend_Registry::isRegistered('acl')){
			$acl = Zend_Registry::get('acl');
			if($acl->has($resource_name)){
				
				foreach($user_roles as $role){
					if($acl->isAllowed($role, $resource_name)){
						$out[] = $role;
					}
				}
				
			}
		}	
		return $out;	
	}
}