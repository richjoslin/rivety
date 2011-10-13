<?php

/*
	Class: Cts_Registry

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		Zend_Registry
*/
class Cts_Registry extends Zend_Registry {

	/* Group: Static Methods */

	/*
		Function: get
			Gets a setting from the registry.

		Arguments:
			setting - The name of the setting.
			default (optional) - The default value of the registry setting, if not found.

		Returns:
			The value of the registry setting.
	*/
	public static function get($setting, $module = "default") {
		$value = null;
		// if we already have it in memory, use that
		if (Zend_Registry::isRegistered($setting)){
			$value = Zend_Registry::get($setting);
		} else {			
			// nope, check the database
			$config_table = new Config();
			if ($config_table->keyExists($module , $setting)) {
				$value = $config_table->get($module, $setting);
			} 	
		}
		return $value;
	}	

}
