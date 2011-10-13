<?php

class Cts_ExtraResourceCheck{
	
	static function isAllowed($resource,$module = "default",$username = null){
		$users_roles_table = new UsersRoles();
		$user_roles = array();
		$roles_table = new Roles();
		if(!is_null($username)){
			$users_roles_db = $users_roles_table->fetchAll($users_roles_table->select()->where("username = ?",$username));
			$user_roles = array();
			if(count($users_roles_db) > 0){
				foreach($users_roles_db as $role){
					$user_roles[] = $role->role_id;	
				}
			}
		} else {
			$user_roles = array($roles_table->getIdByShortname("guest"));			
		}
		
		$resource_name = $module . "-@@EXTRA-" . $resource;
		
		$out = false;
		if(Zend_Registry::isRegistered('acl')){
			$acl = Zend_Registry::get('acl');
			if($acl->has($resource_name)){
				
				foreach($user_roles as $role){
					
					if($acl->isAllowed($role, $resource_name)){
						$out = true;
					}
				}
				
			}
		}		
		return $out;	
	}
}