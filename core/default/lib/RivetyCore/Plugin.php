<?php
/*
	Class: RivetyCore_Plugin

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/

class RivetyCore_Plugin {
 
  // object instance
  private static $instance;
 
  // The protected construct prevents instantiating the class externally.  The construct can be
  // empty, or it can contain additional instructions...
  protected function __construct() {
    	
		if (!Zend_Registry::isRegistered('plugin_actions')) {
			Zend_Registry::set('plugin_actions', array());
		}
		
		if (!Zend_Registry::isRegistered('plugin_filters')) {
			Zend_Registry::set('plugin_filters', array());
		}
  }
 
  // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
  // thus eliminating the possibility of duplicate objects.  The methods can be empty, or
  // can contain additional code (most probably generating error messages in response
  // to attempts to call).
  public function __clone() {
    trigger_error('Clone is not allowed.', E_USER_ERROR);
  }
 
  public function __wakeup() {
    //trigger_error('Deserializing is not allowed.', E_USER_ERROR);
  }
 
  //This method must be static, and must return an instance of the object if the object
  //does not already exist.
  public static function getInstance() {
    if (!self::$instance instanceof self) {
      //RivetyCore_Log::info("Creating plugin manager"); 
      self::$instance = new self;
    }
    return self::$instance;
  }

	/*
		Function: addActionHook
			TBD

		Arguments:
			hook - The name of the hook to add. Ex.: mymodule_default_index_pre_render

		Returns:
			void
	*/
	function addActionHook($hook) {
		RivetyCore_Log::report("RivetyCore_Plugin", "Adding action hook: '" . $hook . "'");
		$plugins = Zend_Registry::get('plugin_actions');
		$plugins[$hook] = array();
		Zend_Registry::set('plugin_actions', $plugins);
	}

	// depricated, just here so things don't break
	function addHook($hook) {
		$this->addActionHook($hook);
	}

	/*
		Function: addFilterHook
			TBD

		Arguments:
			hook - The name of the hook to add. Ex.: mymodule_default_index

		Returns:
			void
	*/
	function addFilterHook($hook) {
		RivetyCore_Log::report("RivetyCore_Plugin", "Adding filter hook: '" . $hook . "'");
		$filters = Zend_Registry::get('plugin_filters');
		$filters[$hook] = array();		
		Zend_Registry::set('plugin_filters', $filters);
	}

	/*
		Function: addFilter
			TBD

		Arguments:
			hook - The name of the hook to add. Ex.: mymodule_default_index
			class_name - The name of the class to add the hook to.
			function_name - The name of the function to execute within the plugin.
			priority - An integer telling the system how to deal with multiple plugins attached to the same hook.

		Returns:
			void
	*/
	function addFilter($hook, $class_name, $function_name, $priority = 10) {
		RivetyCore_Log::report("RivetyCore_Plugin", "Adding filter: '" . $hook . "'");
		$filters = Zend_Registry::get('plugin_filters');
		$filters[$hook][] = array(
			'class_name' => $class_name,
			'function_name' => $function_name,
			'priority' => $priority,
		);
		Zend_Registry::set('plugin_filters', $filters);
	}

	/*
		Function: addAction
			TBD

		Arguments:
			hook - The name of the hook to add. Ex.: mymodule_default_index_pre_render
			class_name - The name of the class to add the hook to.
			function_name - The name of the function to execute within the plugin.
			priority - An integer telling the system how to deal with multiple plugins attached to the same hook.

		Returns:
			void
	*/
	function addAction($hook, $class_name, $function_name, $priority = 10) {
		RivetyCore_Log::report("RivetyCore_Plugin", "Adding action: '" . $hook . "'");
		$plugins = Zend_Registry::get('plugin_actions');
		$plugins[$hook][] = array(
			'class_name' => $class_name,
			'function_name' => $function_name,
			'priority' => $priority,
		);
		Zend_Registry::set('plugin_actions', $plugins);
	}

	/*
		Function: doFilter
			Processes a plugin for a filter hook.

		Arguments:
			hook - The name of the filter hook.
			params - An array of key-value parameters.

		Returns:
			The parameter array that was passed in. with any changes made durung plugin processing.
	*/
	static function doFilter($hook, $params) {
		$message = "RivetyCore_Plugin: Filter hook fired - " . $hook;
		RivetyCore_Log::report($message,null,Zend_Log::INFO);
		//RivetyCore_Log::report("RivetyCore_Plugin: Filter hook fired - " . $hook, $params,Zend_Log::DEBUG);	
		$filters = Zend_Registry::get('plugin_filters');
		if (array_key_exists($hook, $filters)) {
			$priority = array();
			$functions = array();
			foreach ($filters[$hook] as $key => $function) {
				$priority[$key] = $function['priority'];
				$functions[$key] = $function;
			}

			array_multisort($priority, SORT_ASC, $functions, SORT_ASC, $filters[$hook]);
			//RivetyCore_Log::report("RivetyCore_Plugin: Filter priority arrays for ",array('priority' => $priority, 'functions' => $functions, 'plugins-' . $hook => $filters[$hook]), Zend_Log::DEBUG);
			foreach ($filters[$hook] as $action) {
				$class_name = $action['class_name'];
				$function_name = $action['function_name'];				
				$class = new $class_name;
				//RivetyCore_Log::report("RivetyCore_Plugin: Filter " . $hook . " is calling " . $class_name . "::" . $function_name, null, Zend_Log::INFO);
				$params = $class->$function_name($params);
				//RivetyCore_Log::report("RivetyCore_Plugin: " . $class_name . "::" . $function_name . " returned params to " . $hook, $params);
			}
		}
		return $params;
	}

	/*
		Function: doAction
			Processes a plugin for an action hook.

		Arguments:
			hook - The name of the action hook.
			params - An array of key-value parameters.

		Returns:
			void
	*/
	static function doAction($hook, $params) {
		RivetyCore_Log::report("RivetyCore_Plugin: Action hook fired - " . $hook, null, Zend_Log::INFO);
		RivetyCore_Log::report("RivetyCore_Plugin: Action hook fired - " . $hook . " - params: ", $params);	
		$plugins = Zend_Registry::get('plugin_actions');
		if (array_key_exists($hook, $plugins)) {
			$priority = array();
			$functions = array();
			foreach ($plugins[$hook] as $key => $function) {
				$priority[$key] = $function['priority'];
				$functions[$key] = $function;
			}
			
			array_multisort($priority, SORT_ASC, $functions, SORT_ASC, $plugins[$hook]);
			RivetyCore_Log::report("RivetyCore_Plugin: Action priority arrays for " . $hook, array('priority' => $priority, 'functions' => $functions, 'plugins-' . $hook => $plugins[$hook]), Zend_Log::DEBUG);
			foreach ($plugins[$hook] as $action) {
				$class_name = $action['class_name'];
				$function_name = $action['function_name'];
				$class = new $class_name;
				RivetyCore_Log::report("RivetyCore_Plugin: Action " . $hook . " is calling " . $class_name . "::" . $function_name, null, Zend_Log::INFO);
				$class->$function_name($params);
			}
		}
	}
	
	function getHooksInUse(){
		return array(
			"actions" => Zend_Registry::get("plugin_actions"),
			"filters" => Zend_Registry::get("plugin_filters"),
		);
		
	}
	
}
