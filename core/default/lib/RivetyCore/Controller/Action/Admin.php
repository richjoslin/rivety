<?php

/*
	Class: RivetyCore_Controller_Action_Admin

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		Zend_Controller_Action
		<RivetyCore_Controller_Action_Abstract>
*/
abstract class RivetyCore_Controller_Action_Admin extends RivetyCore_Controller_Action_Abstract {

	/*
		Function: init
			Initializes the current instance.

		View Variables:
			isAdminController - boolean - present and true because anything that inherits this is an admin controller
            admin_theme_path - filesystem path to the current admin theme
            admin_theme_url - url to the current admin theme 
            admin_theme_global_path - filesystem path to the current admin theme global folder
            admin_theme_controller_path - filesystem path to the current admin controller template dir
            admin_theme_module_path - filesystem path to the current admin module template dir
            default_admin_theme_path - filesystem path to the default admin theme
            default_admin_theme_url - url to the default admin theme
            default_admin_theme_global_path - filesystem path to the default admin theme global folder
            default_admin_theme_controller_path - filesystem path to the default admin controller template dir
            default_admin_theme_module_path - filesystem path to the default admin module template dir
			current_path - filesystem path to the current theme's controller template path (same as admin_theme_controller_path)
	*/
	function init() {
	    parent::init();
		$template_path = $this->_theme_locations['admin']['current_theme']['path']."/tpl_controllers/".$this->getRequest()->getModuleName();
		$this->view->setScriptPath($template_path);
		$this->view->isAdminController = true;

		$this->view->admin_theme_path                     = $this->_theme_locations['admin']['current_theme']['path'];
		$this->view->admin_theme_url                      = $this->_theme_locations['admin']['current_theme']['url'];
		$this->view->admin_theme_global_path              = $this->_theme_locations['admin']['current_theme']['path']."/tpl_common";
		$this->view->admin_theme_controller_path          = $this->_theme_locations['admin']['current_theme']['path'].'/tpl_controllers/'.$this->getRequest()->getModuleName()."/".$this->getRequest()->getControllerName();
		$this->view->admin_theme_module_path              = $this->_theme_locations['admin']['current_theme']['path'].'/tpl_controllers/'.$this->getRequest()->getModuleName();

		$this->view->default_admin_theme_path             = $this->_theme_locations['admin']['default_theme']['path'];
		$this->view->default_admin_theme_url              = $this->_theme_locations['admin']['default_theme']['url'];
		$this->view->default_admin_theme_global_path      = $this->_theme_locations['admin']['default_theme']['path']."/tpl_common";
		$this->view->default_admin_theme_controller_path  = $this->_theme_locations['admin']['default_theme']['path'].'/tpl_controllers/'.$this->getRequest()->getModuleName()."/".$this->getRequest()->getControllerName();
		$this->view->default_admin_theme_module_path      = $this->_theme_locations['admin']['default_theme']['path'].'/tpl_controllers/'.$this->getRequest()->getModuleName();

		$request = $this->getRequest();

		if ($request->has('dev') && $request->dev == true) {
			$this->view->isDeveloper = true;
		}

		$this->view->current_path = $template_path . "/" . $this->getRequest()->getControllerName();                

		$roles_table = new Roles();
		$locale_table = new Locales();

		if ($this->_identity->isAdmin) {
			$bypass = array();
			$globalRoles = explode(",", RivetyCore_Registry::get('global_role_shortnames'));
			$inherited_roles = array();
			foreach ($this->my_roles as $role => $value) {
				$ids = $roles_table->getAllAncestors($value['id']);
				$inherited_roles = array_merge($inherited_roles, $ids, array($value['id']));
				$all_shortnames = array(array("id" => $value['id'], "shortname" => $value['shortname']));
				foreach ($ids as $bp) {
					$all_shortnames[] = array("id" => $bp, "shortname" => $roles_table->getShortnameById($bp));
				}

				$all_locales = $locale_table->getLocaleCodesArray(true);

				foreach ($all_shortnames as $sn) {
					if (array_key_exists(strtolower(substr($sn['shortname'], -5)),$all_locales) && strtolower(substr($sn['shortname'], -5)) == strtolower($this->locale_code)) {
						$bypass[] = $sn['id']; // if current locale, get other locale restricted roles for that locale for navigation
					}
					if (strtolower(substr($sn['shortname'], -6)) == "global" || in_array($sn['shortname'],$globalRoles) || in_array($sn['id'],$globalRoles)) {
						$bypass[] = $sn['id'];
					}
				}
				
			}
			$inherited_roles = array_unique($inherited_roles);
			sort($inherited_roles);
			$this->view->all_roles = array_unique($inherited_roles);
			$bypass = array_unique($bypass);
			sort($bypass);
			$this->view->bypass = $bypass;
			if (@RivetyCore_ResourceCheck::isAllowed("locale_specific_admin_role", "default", $this->_identity->username)) {
				$this->_bumpRegionalAccess($bypass);
			}

			// This variable is set in $this->_bumpRegionalAccess()
			if (isset($this->restricted_role_id) && count($this->restricted_role_id) > 0) {
				$restr = array();
				foreach ($this->restricted_role_id as $role ) {
					$restr[] = $role['id'];
				}
				$tmp_ids = array_unique($restr);
				$nav_parent_role_ids = array();	
				foreach($tmp_ids as $nav_role){
					$nav_parent_role_ids = array_merge($nav_parent_role_ids, $roles_table->getAllAncestors($nav_role));
				}
				$nav_role_ids = array_merge($nav_parent_role_ids, $tmp_ids, $bypass);
				$unique_ids = array_unique($nav_role_ids);

				$nav_table = new Navigation($unique_ids, $this->locale_code);
				
				$cache = new RivetyCore_Cache();
				$cache_name = 'navigation_admin_'.$this->locale_code.'-'.md5(implode($unique_ids,"-"));	// MD5 The Unique IDs to shorten the cache name
				$cache_tags = array('navigation', 'admin_navigation', $this->locale_code);

				$nav_items_temp = $cache->load($cache_name);
				if ($nav_items_temp === false || !isset($nav_items_temp)) {
					$nav_items_temp = array();
					foreach ($unique_ids as $nav_role_id) {
						$nav_items_temp = array_merge($nav_items_temp, $nav_table->getNavTree($nav_role_id));
					}
					$cache->save($nav_items_temp, $cache_name, $cache_tags);
				}

				$navparams = array('nav_items' => $nav_items_temp, 'request' => $this->_request, 'locale_code' => $this->locale_code);
				$navparams = $this->_rivety_plugin->doFilter('controller_nav', $navparams); // FILTER HOOK
				$this->view->nav_items = $navparams['nav_items'];
				$this->view->access = $this->restricted_role_id;
			} else {
				$access = array();
				$roles = $inherited_roles;
				foreach ($roles as $role) {
					$in = $this->_checkMatch($role);
					if (count($in) > 0) {
						foreach ($in as $i) {
							$access[] = array("id"=>$i,"shortname"=>$roles_table->getShortnameById($i));
						}
					}
				}
				$this->view->access = $access;
				
			}

		}

	}

	/*
		Function: _bumpRegionalAccess
	*/
	public function _bumpRegionalAccess ($bypass = null) {
		$roles_table = new Roles();
		$roles_resources_table = new RolesResources();

		if (isset($this->resource_locale)) {
			$resource_locale = $this->resource_locale;
		} else {
			$resource_locale = $this->locale_code;
		}
		$role_lock = array();
		if ($this->_identity->isAdmin) {
			$role_lock = @RivetyCore_ResourceCheck::isAllowed("locale_specific_admin_role", "default", $this->_identity->username);
		}
		if(count($role_lock) > 0){ //user is under some type of locale restriction
			$shortnames = array();
			if (is_array($role_lock)){
				foreach ($role_lock as $i) {
					$shortnames[] = $roles_table->getShortnameById($i); //get the shortnames of the locked roles
				}
			}	
			$match = array();
			foreach ($shortnames as $sn) {
				if(stristr($sn,$resource_locale)){  //we've got an access match to a shortname locale
					$match[] = array("id" => $roles_table->getIdByShortname($sn), "shortname" => $sn);
				} else {
					$no_match[] = array("id" => $roles_table->getIdByShortname($sn), "shortname" => $sn);
				}
			}
			$access = array();
			if (count($match) > 0 || count($bypass) > 0) {

				if (count($match) > 0) {
					foreach ($match as $m) {
						$m_in = $this->_checkMatch($m['id']);
						if (count($m_in) > 0) {
							foreach ($m_in as $m) {
								$access[] = array("id"=>$m,"shortname"=>$roles_table->getShortnameById($m));
							}
						}
					}
				}
				if (count($bypass) > 0) {
					foreach ($bypass as $bp) {
						$b_in = $this->_checkMatch($bp);
						if (count($b_in) > 0) {
							foreach ($b_in as $b) {
								$access[] = array("id"=>$b,"shortname"=>$roles_table->getShortnameById($b));
							}
						}
					}
				}
			}
			if (count($access) === 0) { //if no access we have to put them somewhere they belong.
				$allowed = array();
				foreach ($shortnames as $allowed_locales) {
					
					$allowed[] = strtolower(substr($allowed_locales, -5));
				}
				if (!in_array($this->locale_code,$allowed) && count($allowed) > 0) {
					$this->locale_code = $allowed[0];
				}
				$this->_redirect('/default/admin/index'); // bump to dashboard.
				
			} else {
				$this->restricted_role_id = $access;
				return $access;
			}
		} 
	}	

	/*
		Function: _checkMatch
	*/
	protected function _checkMatch($match) {
		$roles_resources_table = new RolesResources();
		$roles_table = new Roles();
		$this_access = array();
		$request = $this->getRequest();
		$resource_name = 	$request->getModuleName() . "-" .ucfirst(strtolower($request->getControllerName())) . "-" .$request->getActionName();
		if(Zend_Registry::isRegistered('acl')){
			$acl = Zend_Registry::get('acl');
			
			if($acl->has($resource_name)){

				if($acl->isAllowed($match, $resource_name)){
					$this_access[] = $match;
				}

				$indirect = $roles_table->getAllAncestors($match); //maybe they inherit access
				$indirect_access = array();
				
				foreach($indirect as $role){
					if($acl->isAllowed($role, $resource_name)){
						$this_access[] = $role;
						$this_access[] = $match;
					}
				}
				
			}
					
		}
		return array_unique($this_access); // in not null, they inherit access
	}

}
