<?php

/*
	Class: UsersIndex

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		<RivetyCore_Db_Table_Abstract>
*/
class UsersIndex extends RivetyCore_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
    protected $_name = 'default_vw_users_index';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
    protected $_primary = 'username';

	/*
		Variable: $_keyword_search_field_names
	*/
	public $_keyword_search_field_names = array("username", "full_name");

	/* Group: Instance Methods */

    /*
		Function: fetchByUsername
			Retrieves all data about one particular user from the database.

		Arguments:
			username - The username of the user to fetch.

		Returns: Zend_Db_Table_Row
	*/
    public function fetchByUsername($username) {
		$where = $this->getAdapter()->quoteInto('username = ?', $username);
        return $this->fetchRow($where);
    }   

    /*
		Function: userExists
			Verifies the existence of a user in the database.

		Arguments:
			username - The username of the user for which to verify its existence.

		Returns: boolean
	*/
    public function userExists($username) {
    	$where = $this->getAdapter()->quoteInto('username = ?', $username);
    	$user = $this->fetchRow($where);
    	if (!is_null($user)) {
    		return true;
    	} else {
    		return false;
    	}    
    }

}
