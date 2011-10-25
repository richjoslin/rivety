<?php

/*
	Class: Plugins

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
	 	<RivetyCore_Db_Table_Abstract>
*/
class Plugins extends RivetyCore_Db_Table_Abstract
{

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
	protected $_name = 'default_plugins';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
	protected $_primary = array('hook_name', 'class_name', 'method_name');

	/* Group: Instance Methods */

	/*
		Function: exists

		Arguments:
			hook_name - TBD
			class_name - TBD
			method_name - TBD

		Returns: boolean
	*/
	function exists($hook_name, $class_name, $method_name)
	{
		$where = $this->getAdapter()->quoteInto("hook_name = ? and ", $hook_name);
		$where .= $this->getAdapter()->quoteInto("class_name = ? and ", $class_name);
		$where .= $this->getAdapter()->quoteInto("method_name = ?", $method_name);
		if ($this->getCountByWhereClause($where) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}
