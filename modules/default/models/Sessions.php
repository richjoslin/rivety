<?php

/*
	Class: Sessions

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
	 	<Cts_Db_Table_Abstract>
*/
class Sessions extends Zend_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
	protected $_name = 'default_sessions';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
	protected $_primary = 'id';    

	/* Group: Has No Methods */

	public function getCountByWhereClause($whereclause = null) {
		$db = $this->getAdapter();
		$sql = "SELECT count(*) from " . $this->_name;
		if (!is_null($whereclause)) {
			$sql .= " where " . $whereclause;
		}
		$total = $db->fetchOne($sql);
		return $total;
	}

}
