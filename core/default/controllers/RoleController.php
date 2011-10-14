<?php

/*
	Class: Role

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>
*/
class RoleController extends RivetyCore_Controller_Action_Admin {

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			Initializes the current instance.
			Also initializes the parent object (calls init() on the parent instance).
	*/
    function init() {
        parent::init();
    }

	/* Group: Actions */

	/*
		Function: index
			Description of what this action does.

		View Variables:
			roles - array containing all roles from the db. Uses <RolesIndex> view.
			
	*/
    function indexAction() {
        $roles = new Roles();
        $this->view->roles = $roles->fetchAll($roles->select()->order('shortname asc'))->toArray();
    }

	/*
		Function: edit
			Creates or edits roles.

		HTTP GET or POST Parameters:
			param - description of param

		Registry Values:
			registry_value - description of registry value used in this action

		Plugin Hooks:
			- *hook_name* (filter|action) - description of when hook is called
				param param_name - description of param passed to hook

		View Variables:
			$view_var - Description of variable that gets passed to view
			
	*/
    function editAction() {
    	
		$request = new RivetyCore_Request($this->getRequest());
		$roles_table = new Roles();
		$role = null;
		
		if ($request->has('id')) {
			if(!is_null($request->id)){
				$role = $roles_table->fetchRow($roles_table->select()->where("id = ?",$request->id));
				
				if(!is_null($role)){
					// we do not edit the guest role
					if($role->shortname == "guest"){
						$this->_redirect("/default/role");
					}
				
					$this->view->role 			= $role->toArray();
					$this->view->role_tree 		= $roles_table->getRoleTree(null,$role->id);			
					$this->view->inherited_ids 	= $roles_table->getInheritedRoles($role->id);
				}
			}
		} 		
			
		if(is_null($role)){
			$this->view->role_tree = $roles_table->getRoleTree();	
		}
				

		if ($this->getRequest()->isPost()) {
            
            $errors = array();
		    if($request->has('inherit_role')){
		    	$parents = array();
		    	foreach($request->inherit_role as $inherit_role){
		    		$parents = array_merge($parents, $roles_table->getAllAncestors($inherit_role));
		    	}
		    	$inherit_ids = array();
		    	foreach($request->inherit_role as $inherit_role){
		    		if(!in_array($inherit_role,$parents)){
		    			$inherit_ids[] = $inherit_role;
		    		}	
		    	}
		    } 
		    
            if ($request->has('shortname')) {
              $shortname = $request->shortname;
              if (!RivetyCore_Validate::checkLength($request->shortname, 1, 255)) {
                $errors[] = $this->_T("Shortname must be between 1 and 255 chars.");
              }
            } else {
              $errors[] = $this->_T("Shortname is a requried field.");
            }

			$description = $request->description;
			$isadmin = (int)$request->checkbox('isadmin');
			if (count($errors) == 0) {
				$data = array(
					'shortname' => $shortname,
					'description' => $description,
					'isadmin' => $isadmin,
				);

				//If we have an id, this is an update.
				$id = (int)$this->_request->getPost('id');
				if ($id != 0) {
					$where = 'id = ' . $id;
					$roles_table->update($data, $where);
				} else {
					//We don't, this is an insert.
					$id = $roles_table->insert($data);					
				}
				$roles_table->removeInheritedRole($id);
				foreach($inherit_ids as $in_id){					
					$roles_table->setInheritedRole($id,$in_id);
				}				
				$this->_redirect("/default/role");				
			} else {
			  $this->view->errors = $errors;
			}
		}
		
		if ($request->has('id')) {
			// this is an edit
			$id = $request->id;
			
			if ($id > 0) {
				$this->view->role = $roles_table->fetchRow('id = ' . $id)->toArray();
					
			}
			
			$this->view->inherited_ids 	= $roles_table->getInheritedRoles($id);	
			
		} else {
		
			foreach ($roles_table->fetchAll()->toArray() as $role) {
				$role_choices[$role['id']] = $role['shortname'];
			}
				
			$this->view->role_choices = $role_choices;
		}
		
    }        

	/*
		Function: delete
			Delete a role.
			
		HTTP POST Parameters:
			delete - Value must be yes or this doesn't work.
			id - The id of the role we're editing (mainly for redirect purposes).

		View Variables:
			role - array containing the role
			success - success message, if any
			errors - array of error messages, if any			
	*/
	function deleteAction(){
		$request = new RivetyCore_Request($this->getRequest());
		$roles_table = new Roles();
		
		if($request->has('id')){
		  $id = $request->id;
		  $role = $roles_table->fetchRow("id = ".$id);
		  if(is_null($role)){
		    $this->_redirect('/default/role');
		  }
		} else {
		  $this->_redirect('/default/role');
		}
        
		if ($this->getRequest()->isPost() and $request->has("delete")) {

		  $errors = array();

		  // can't be last admin
		  if ((boolean)$role->isadmin and $roles_table->getCountByWhereClause("isadmin = 1") == 1) {		  	
		    $errors[] = $this->_T("This is the only admin role. It cannot be deleted.");		    
		  }

		  // can't be guest
		  if ((boolean)$role->isguest) {
		    $errors[] = $this->_T("This is the guest role. It cannot be deleted.");
		  }
		  
		  // can't be default
		  if ((boolean)$role->isdefault) {
		    $errors[] = $this->_T("This is the default role. It cannot be deleted.");
		  }

		 // can't have any users
		 $userwhereclause = "role_id = " . $role->id;
		 $users_table = new UsersRoles();
		 if($users_table->getCountByWhereClause($userwhereclause) > 0){
		 	$errors[] = $this->_T("This role cannot be deleted because there are users assigned to it.");
		 }

		  // can't have children
		  $inherited_by = $roles_table->fetchImmediateChildren($role->id);
		  if(count($inherited_by) > 0){
		    $error = $this->_T("This role is inherited by role(s) ");
		    $firstpass = true;
		    
		    foreach($inherited_by as $role_i){
		      if($firstpass){
		        $firstpass = false;
		      } else {
		        $error .= ", ";
		      }
		      $error .= $role_i->shortname;
		      
		    }
		    $error .= $this->_T(". It cannot be deleted.");

		    $errors[] = $error;
		  }

			if ($request->delete == "Yes") {
				if (count($errors) > 0) {
					$this->view->errors = $errors;
				} else {
					$roles_table->delete("id = ".$id);
					$this->view->success = $this->_T("Role deleted.");
				}
			} else {
				$this->_redirect("/default/role");
			}
		}
		$this->view->role = $role->toArray();
		
	}    

}
