<?php

/*
	Class: RivetyCore_View_Renderer

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		Zend_View_Interface
*/
class RivetyCore_View_Renderer implements Zend_View_Interface
{

	/* Group: Variables */

	/*
		Variable: $_smarty
			Smarty object.
	*/
	public $_smarty;

	/* Group: Constructors */

	/*
		Constructor: __construct

		Arguments:
			tmplPath - TBD
			extraParams - TBD
	*/
	public function __construct($tmplPath = null, $extraParams = array())
	{
		$this->_smarty = new RivetyCore_View_Smarty;

		if (!is_null($tmplPath))
		{
			$this->setScriptPath($tmplPath);
		}

		foreach ($extraParams as $key => $value)
		{
			if ($key != "plugins_dir")
			{
				$this->_smarty->$key = $value;
			}
			else
			{
				$plugin_dirs = array('plugins');
				if (is_array($value))
				{
					foreach ($value as $plugin_dir)
					{
						$plugin_dirs[] = $plugin_dir;
					}
				}
				else
				{
					$plugin_dirs[] = $value;
				}
				$this->_smarty->plugins_dir = $plugin_dirs;
			}
		}

	}

	/* Group: Instance Methods */

	/*
		Function: getEngine
			Return the template engine object.

		Arguments:
			none

		Returns:
			The current Smarty template engine object.
	*/
	public function getEngine()
	{
		return $this->_smarty;
	}

	/*
		Function setScriptPath
			Set the path to the templates.

		Arguments
			path - The directory to set as the path.

		Returns: void
	*/
	public function setScriptPath($path)
	{
		$this->_smarty->template_dir = $path;
	}

	/*
		Function: getScriptPaths
			Retrieve the current template directory.

		Arguments:
			none

		Returns:
			array
	*/
	public function getScriptPaths()
	{
		return array('script' => $this->_smarty->template_dir);
	}

	/*
		Function: setBasePath
			Alias for setScriptPath.

		Arguments:
			path - TBD
			prefix (optional) - TBD - Default is 'Zend_View'.

		Returns:
			void
	*/
	public function setBasePath($path, $prefix = 'Zend_View')
	{
		return $this->setScriptPath($path);
	}

	/*
		Function: addBasePath
			Alias for setScriptPath.

		Arguments:
			path

		Returns:
			void
	*/
	public function addBasePath($path, $prefix = 'Zend_View')
	{
		// this is the first time setScriptPath is called

		return $this->setScriptPath($path);
	}

	/*
		Function: __set
			Assign a variable to the template.

		Arguments:
			key - The variable name.
			val - The variable value.

		Returns:
			void
	*/
	public function __set($key, $val)
	{
		$this->_smarty->assign($key, $val);
	}

	/*
		Function: __get
			Retrieve an assigned variable.

		Arguments:
			key - The variable name.

		Returns:
			The variable value.
	*/
	public function __get($key)
	{
		return $this->_smarty->get_template_vars($key);
	}

	/*
		Function: __isset
			Allows testing with empty() and isset() to work.

		Arguments:
			key - The variable name.

		Returns:
			boolean
	*/
	public function __isset($key)
	{
		return (null !== $this->_smarty->get_template_vars($key));
	}

	/*
		Function: __unset
			Allows unset() on object properties to work.

		Arguments:
			key - The variable name.

		Returns:
			void
	*/
	public function __unset($key)
	{
		$this->_smarty->clear_assign($key);
	}

	/*
		Function: assign
			Assign variables to the template.
			Allows setting a specific key to the specified value,
			OR passing an array of key => value pairs to set en masse.

		Arguments:
			spec - The assignment strategy to use (key or array of key => value pairs)
			value (optional) - If assigning a named variable, use this as the value.

		Returns:
			void
	*/
	public function assign($spec, $value = null)
	{
		if (is_array($spec))
		{
			$this->_smarty->assign($spec);
			return;
		}
		$this->_smarty->assign($spec, $value);
	}

	/*
		Function: clearVars
			Clears all variables assigned to Zend_View either via {@link assign()} or property overloading ({@link __get()}/{@link __set()}).

		Arguments:
			none

		Returns:
			void
	*/
	public function clearVars()
	{
		$this->_smarty->clear_all_assign();
	}

	/*
		Function: render
			Processes a template and returns the output.

		Arguments:
			name - The template to process.

		Returns:
			string
	*/
	public function render($name)
	{
		
		// theme template file
		$template_file = $this->_smarty->template_dir . _DS . $name;
		
		// module views template
		$module_template_file = $this->_smarty->_tpl_vars['module_views_controller_path'] . _DS . $name;
		
		
		//RivetyCore_Log::debug("Looking for " . $template_file);
		
		if (!file_exists($template_file))
		{
			
			//RivetyCore_Log::debug("Falling back to " . $module_template_file);
			if (is_readable($module_template_file))
			{
					// fall back to looking in module's views folder					
					$template_file =  $module_template_file;

			}
			else
			{
				// We're out of ideas. Sorry.
				throw new Zend_Exception("Provided template path is not valid: ". $name);
			}
		}
		if (array_key_exists("mca", $this->_smarty->_tpl_vars))
		{
			$mca = $this->_smarty->_tpl_vars['mca'];
			$this->_smarty->compile_id = $mca;
		}
		return $this->_smarty->fetch($template_file);
	}

	/*
		Function: display
			Display the template.

		Arguments:
			name - The path and filename of template to process.

		Returns:
			void
	*/
	public function display($name)
	{
		$this->_smarty->display($name);
	}
}
