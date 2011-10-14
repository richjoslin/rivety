<?php

/*
	Class: Module
		This is how communit.as modules are installed, enabled, disabled and removed.
		
	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>
		<RivetyCore_Controller_Action_Admin>
		<Modules>
*/
class ModuleController extends  RivetyCore_Controller_Action_Admin {

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			For this class, does nothing other than initialize the parent object (calls init() on the parent instance).
	*/
	function init(){
        parent::init();
    }

	/* Group: Actions */

	/*
		Function: index
			It will look in the modules directory
			for newly added modules (which must conform to the module structure and have a properly formatted module.ini) 
			
			If you're looking for the plugin hooks, they're in the <Modules> model class.

		HTTP GET or POST Parameters:
			id - module id of the module to be operated on
			perform - action to perform. options are enable,disable,install and uninstall

		View Variables:
			modules - an array of module data
			success - the success message if there is one
			errors - an array containing errors if there are any
			notice - a notice message if there is one
			
	*/
	
    function indexAction(){
    	$modules_table = new Modules('modules');
    	$request = new RivetyCore_Request($this->getRequest());

    	if($request->has("id") and $request->has("perform")){
    			
	    		switch($request->perform){
	    			case "enable":
		    			if(!$modules_table->isEnabled($request->id)){
		    				  if($modules_table->enable($request->id)){
		    				  	if(!is_null($modules_table->success)){
		    				  		$this->view->success = $modules_table->success;
		    				  	} else {
		    				  		$this->view->success = "Module \"".$request->id ."\" enabled.";
		    				  	}	
		    				  }
		    			} else {
		    				$this->view->notice = "Module \"".$request->id ."\" is already enabled.";
		    			}
	    			break;
	    			
	    			case "disable":
	    				if($modules_table->isEnabled($request->id)){
		    				  if($modules_table->disable($request->id)){
		    				  	if(!is_null($modules_table->success)){
		    				  		$this->view->success = $modules_table->success;
		    				  	} else {
		    				  		$this->view->success = "Module \"".$request->id ."\" disabled.";
		    				  	}	
		    				  }  				  
		    			} else {
		    				$this->view->notice = "Module \"".$request->id ."\" is already disabled.";
		    			}
	    			break;
	    			
	    			case "install":
	    				
	    				if(!$modules_table->exists($request->id)){
	    					if($modules_table->install($request->id)){
		    				  	if(!is_null($modules_table->success)){
		    				  		$this->view->success = $modules_table->success;
		    				  	} else {
		    				  		$this->view->success = "Module \"".$request->id ."\" installed.";
		    				  	}	
		    				  }  
	    				} else {
	    					$this->view->notice = "Module \"".$request->id ."\" is already installed.";
	    				}	    			
	    			break;
	    			
	    			case "uninstall":
	    				if($modules_table->exists($request->id)){
		    				 if($modules_table->disable($request->id)){
		    				 	if($modules_table->uninstall($request->id)){
			    				  	if(!is_null($modules_table->success)){			    				  						  		
			    				  		$this->view->success = $modules_table->success;
			    				  	} else {
			    				  		$this->view->success = "Module \"".$request->id ."\" disabled and uninstalled.";
			    				  	}	
		    				  	} 
		    				 }
	    				} else {
	    					$this->view->notice = "Module \"".$request->id ."\" is not installed.";
	    				}
	    			break;	
	    			
	    		
    		}
    		if(count($modules_table->errors) > 0){
    			$this->view->errors = $modules_table->errors;
    		}
    		
    		if(!is_null($modules_table->notice)){
    			$this->view->notice = $modules_table->notice;
    		}    		

    	}
    	
    	$basepath = Zend_Registry::get('basepath');
		$module_dir = $basepath."/modules";
		$o_module_dir = dir($module_dir );
		$available_modules = array();
		
		while (false !== ($entry = $o_module_dir->read())) {
			if (substr($entry, 0, 1) != "."){
				if($entry != "default"){
					$full_dir = $module_dir . "/" . $entry;
					if(file_exists($full_dir . "/module.ini") and !$modules_table->exists($entry)){
						$tmp_module = $modules_table->parseIni($entry);
						$tmp_module['id'] = $entry;
						$tmp_module['available'] = true;	
		   				$available_modules[] = $tmp_module;
		   			}					
				}		
			}
		}

		$o_module_dir->close();
    	
		$tmp_modules = array();
    	$modules = $modules_table->fetchAll(null,"id");
		
		
    	if(count($modules) > 0){
    		$tmp_modules = array();
    		foreach($modules as $module){
    			$module = $module->toArray();
    			try{
    				$config = $modules_table->parseIni($module['id']);    			
    				foreach($config as $key => $val){
    					$module[$key] = $val;
    				}
    				$module['available'] = false;
    				$tmp_modules[] = $module;
    			} catch (Exception $e){
    				RivetyCore_Log::report("Could not set up ".$module, $e, Zend_Log::ERR);
    			}
    		}			
    		
			
    	}
    	$this->view->modules = array_merge($tmp_modules,$available_modules);
    	
    	
    }
	/*
		Function: plugin
			This allows you to configure the plugin priority for a particular hook. It doesn't actually work yet. At the moment, all plugins get 
			a priority of 10.

		View Variables:
			hooks - An array of hooks that are in use
			
	*/
	function pluginAction(){
		$hooks = $this->_rivety_plugin->getHooksInUse();
		$this->view->hooks = $hooks;		
	}
	
	function uninstallAction(){
		$request = new RivetyCore_Request($this->getRequest());
		if($request->has('id')){
			$this->view->id = $request->id;
			$this->view->notice = $this->_T("You are about to uninstall a module. This cannot be undone.");
		} else {
			$this->_redirect('/default/module/index');
		}
		
		if ($this->getRequest()->isPost()) {
			$del = strtolower($request->delete);
						
			if($del == 'yes' && $request->has('id')){
				$this->_redirect("/default/module/index/id/".$request->id."/perform/uninstall");
			} else {
				$this->_redirect('/default/module/index');
			}	
		}
				
	}

    
}