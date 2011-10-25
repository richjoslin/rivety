<?php

/*
	Class: DatabaseVersions

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
	 	<RivetyCore_Db_Table_Abstract>
*/
class DatabaseVersions extends RivetyCore_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
    protected $_primary = 'id';    

	/* Group: Instance Methods */

	/*
		Function: _setupTableName

		Arguments: none

		Returns: void
	*/
	protected function _setupTableName() {
		if (!in_array("default_database_versions", $this->getAdapter()->listTables())) {
			// table does not exist. we need to create it first.
			$script = new RivetyCore_Db_Script("default", "create_database_versions");
			if (!$script) {
				throw new Exception("CANT_UPGRADE - Cannot upgrade the database because versions table does not exist and couldn't be created.");
			}
		}
		$this->_name = 'default_database_versions';
		parent::_setupTableName();
	}

	/*
		Function: setCurrentVersion

		Arguments:
			module_id - TBD
			version - TBD

		Returns: void
	*/
	function setCurrentVersion($module_id, $version) {
		$where = $this->getAdapter()->quoteInto("id = ?", $module_id);
		$data = array("db_version" => $version);
		if ($this->getCountByWhereClause($where) > 0) {
			$this->update($data, $where);
		} else {
			$data['id'] = $module_id;
			$this->insert($data);
		}
	}

	/*
		Function: getCurrentVersion

		Arguments:
			module_id - TBD

		Returns: TBD
	*/
	function getCurrentVersion($module_id) {
		$db_version = $this->fetchRow($this->select()->where('id = ?', $module_id));
		if (is_null($db_version)) {
			return null;
		} else {
			return $db_version->db_version;
		}
	}

}
