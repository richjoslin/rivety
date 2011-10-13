<?php

/*
	Class: RolesRoles

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
	 	<Cts_Db_Table_Abstract>
*/
class RolesRoles extends Cts_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
	protected $_name = 'default_roles_roles';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
	protected $_primary = array('role_id','inherits_role_id');    

	/* Group: Has No Methods */

}