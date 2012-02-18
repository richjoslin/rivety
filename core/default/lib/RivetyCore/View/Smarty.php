<?php

/*
	Class: RivetyCore_View_Smarty

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_View_Smarty extends Smarty
{

	function _smarty_include($params)
	{
		$theme_global = $this->_tpl_vars['theme_global_path'];
		$default_global =  $this->_tpl_vars['default_global_path'];
		
		$file = substr( $params['smarty_include_tpl_file'], strlen("file:"));
		if (!file_exists($file))
		{
			$params['smarty_include_tpl_file'] = str_replace($theme_global, $default_global, $params['smarty_include_tpl_file']);
		}
		$file = substr( $params['smarty_include_tpl_file'], strlen("file:"));
		if (!file_exists($file))
		{
			throw new Exception("MISSING_TEMPLATE - The template file does not exist: " . $file);
		}
		return parent::_smarty_include($params);
	}

}
