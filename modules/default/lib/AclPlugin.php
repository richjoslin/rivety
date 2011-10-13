<?php
/**
 * communit.as
 * @copyright (C)2008 Jaybill McCarthy, All Rights Reserved.
 * @category communitas
 * @package communitas
 * @author Jaybill McCarthy
 * @link http://communit.as communit.as
 * @license http://communit.as/docs/license License
 */

/**
 *
 * @package communitas
 * @subpackage core_lib
 * @license http://communit.as/docs/license License 
 */
 
class AclPlugin extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$frontController = Zend_Controller_Front :: getInstance();
		$auth = Zend_Auth :: getInstance();
		$roles_table = new Roles();   
		
		$appNamespace = new Zend_Session_Namespace('Cts_Temp');

		if (Zend_Registry :: isRegistered('acl')) {
			$acl = Zend_Registry :: get('acl');
		} else {
			$acl = new Cts_Acl($auth);
			Zend_Registry::set('acl', $acl);
		}

		// determine role
		if ($auth->hasIdentity()) {
			$user = Zend_Auth :: getInstance()->getIdentity();
			$users_roles_table = new UsersRoles();
			$users_roles_db = $users_roles_table->fetchAll($users_roles_table->select()->where("username = ?",$user->username));
			$user_roles = array();
			if(count($users_roles_db) > 0){
				foreach($users_roles_db as $role){
					$user_roles[] = $role->role_id;
					$user_roles = array_merge($user_roles, $roles_table->getAllAncestors($role->role_id));	
				}
			}
			
			$user_roles = array_unique($user_roles);
			$user_is_guest = false;
			         
            $defaultNamespace = new Zend_Session_Namespace('Zend_Auth'); 
	        $defaultNamespace->setExpirationSeconds(86400);
		} else {
			$user_roles = array($roles_table->getIdByShortname("guest"));
			$user_is_guest = true;
		}
		

		$requested = $request->getModuleName() . "-" .
		ucfirst(strtolower($request->getControllerName())) . "-" .
		$request->getActionName();
		//dd($requested);
		$url = $frontController->getBaseUrl() . "/";


		if(!$acl->has($requested)){
			// this doesn't exist, throw to 404
			$request->setModuleName('default');
			$request->setControllerName('auth');
			$request->setActionName('missing');
		} else {
		
			$isAllowed = array();
			foreach($user_roles as $user_role){
				if ($acl->isAllowed($user_role, $requested)){
					$isAllowed[$user_role] = true;
				} else {
					$isAllowed[$user_role] = false;
				}
			}
			if(!in_array(true,$isAllowed)){
				if ($user_is_guest) {
					$url .= $request->getModuleName() . "/";
					$url .= $request->getControllerName() . "/";
					$url .= $request->getActionName() . "/";

					$params = $request->getParams();					
					while ($param = current($params)) { 
				    	if(key($params) != "module" and 
				    	key($params) != "controller" and
				    	key($params) != "action")
			    	  				
	        			$url .= key($params).'/'.$param ."/";    				
	    				next($params);
    				
					}			
					if(substr($url,strlen($url) - 1,1) == "/"){
						$url = substr($url,0,strlen($url) - 1);
					}
							
						//Zend_debug::dump($params);					
						//Zend_debug::dump($url);
					
						// place requested url in the sesson, 
						// unless this is the login controller				
					
						if ($request->getControllerName() != "auth"){
							$request->setParam('url', base64_encode($url));
							//$appNamespace->requestedUrl = $url;
						}
					
						// send on to the login scipt
						$request->setModuleName('default');
						$request->setControllerName('auth');
						$request->setActionName('login');
				
					} else {
						$admin = "default-Admin-index";
						$isAdmin = array();
						foreach($user_roles as $user_role){
							if ($acl->isAllowed($user_role, $admin)){
								$isAdmin[$user_role] = true;
							} else {
								$isAdmin[$user_role] = false;
							}
						}
						if (!in_array(true,$isAdmin)) {
							$request->setModuleName('default');				
							$request->setControllerName('auth');
							$request->setActionName('denied');	
						} else {
							$request->setModuleName('default');				
							$request->setControllerName('admin');
							$request->setActionName('index');	
						}
					}
				}
			
			}
	
	}
}
