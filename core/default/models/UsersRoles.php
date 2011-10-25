<?php

/*
	Class: UsersRoles

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
	 	<RivetyCore_Db_Table_Abstract>
*/
class UsersRoles extends RivetyCore_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
	protected $_name = 'default_users_roles';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
	protected $_primary = array('username', 'role_id');    

	/* Group: Has No Methods */

	/*
		Function: isUserInRole
			Indicates whether a certain user has a certain role.

		Arguments:
			username - The username to verify.
			role_id - The ID (integer) of the role to verify.
	*/
	function isUserInRole($username, $role_id) {
		$where = $this->getAdapter()->quoteInto("username = ?", $username);
		$where .= $this->getAdapter()->quoteInto(" and role_id = ?", $role_id);
		return ($this->getCountByWhereClause($where) > 0);
	}
}
