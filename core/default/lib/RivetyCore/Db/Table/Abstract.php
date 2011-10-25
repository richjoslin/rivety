<?php

/*
	Class: RivetyCore_Db_Table_Abstract

	About: Author
		Jaybill McCarthy and Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		- Zend_Db_Table
*/
abstract class RivetyCore_Db_Table_Abstract extends Zend_Db_Table
{

	/* Group: Properties */

	/*
		Property: $_errors
	*/
	public $_errors;

	/*
		Property: $_keyword_search_field_names
	*/
	public $_keyword_search_field_names = array();

	/*
		Property: $_module_id
	*/
	public $_module_id = "default";

	/* Group: Constructors */

	/*
		Constructor: RivetyCore_Db_Table_Abstract
			Calls the parent's constructor and instantiates a logger. Also sets up a DB adapter and the _rivety_plugin variable.

		Arguments:
			config (optional) - TBD
	*/
	function RivetyCore_Db_Table_Abstract($config = null)
	{
		$this->_errors = array();
		if (isset($this->_use_adapter))
		{
			$dbAdapters = Zend_Registry::get('dbAdapters');
			$config = ($dbAdapters[$this->_use_adapter]);
		}
		$this->_rivety_plugin = RivetyCore_Plugin::getInstance();
		return parent::__construct($config);
	}

	/* Group: Instance Methods */

	/*
		Function: getCountByWhereClause
			Gets a count of records using the supplied where clause.

		Arguments:
			whereclause (optional) - A string with your where clause in it. The word "where" is not needed. If you provide no where clause, a count of all rows will be returned.

		Returns:
			An integer representing the total number of records for that where clause.
	*/
	public function getCountByWhereClause($whereclause = null)
	{
		$db = $this->getAdapter();
		$sql = "SELECT count(*) FROM " . $this->_name;
		if (!is_null($whereclause)) $sql .= " where ".$whereclause;
		$total = $db->fetchOne($sql);
		return $total;
	}

	/*
		Function: update
			Updates the table with the supplied data array.
			You must supply a where clause if you want to limit what gets updated,
			otherwise all rows will get the update.

		Arguments:
			data - An array with a key-value pair for each column in the table.
			where - A where clause string to limit the rows that get updated. If the where clause is empty, all rows will be updated with the data argument.

		Returns:
			TBD
	*/
	public function update(array $data, $where)
	{
		$metadata = $this->info();
		$columns = $metadata['cols'];
		$timestamp = date("Y-m-d H:i:s") ;
		if (in_array('updated_on', $columns))
		{
			if (!in_array('updated_on', $data))
			{
				$data['updated_on'] = $timestamp;
			}
		}
		$params = array(
			"data" => $data,
			"where" => $where,
			"errors" => $this->_errors,
			"table" => $this->_name,
			"module" => $this->_module_id,
			"primary" => $this->_primary,
		);
		if (isset($this->_use_adapter))
		{
			$params['use_adapter'] = $this->_use_adapter;
		}
		else
		{
			$params['use_adapter'] = null;
		}
		// rethrowing exceptions here because of a weird php issue where the trace isn't getting passed
		try
		{
			$params = RivetyCore_Plugin::getInstance()->doFilter('db_table_update', $params);
		}
		catch (Exception $e)
		{
			throw($e);
		}
		if (count($params['errors']) == 0)
		{
			return parent::update($params['data'], $params['where']);
		}
		else
		{
			$this->_errors = $params['errors'];
			return false;
		}
	}

	/*
		Function: insert
	*/
	public function insert(array $data)
	{
		$params = array(
			"data" => $data,
			"errors" => $this->_errors,
			"table" => $this->_name,
			"module" => $this->_module_id,
			"primary" => $this->_primary,
		);
		if (isset($this->_use_adapter)) $params['use_adapter'] = $this->_use_adapter;
		else $params['use_adapter'] = null;
		// rethrowing exceptions here because of a weird php issue where the trace isn't getting passed
		try
		{
			$params = RivetyCore_Plugin::getInstance()->doFilter('db_table_insert', $params);
		}
		catch (Exception $e)
		{
			throw($e);
		}
		if (count($params['errors']) == 0)
		{
			$params['insert_id'] = parent::insert($params['data']);
			RivetyCore_Plugin::getInstance()->doAction('db_table_post_insert', $params);
			return $params['insert_id'];
		}
		else
		{
			$this->_errors = $params['errors'];
			return false;
		}
	}

	/*
		Function: dumpDataToXml
			Convert a rowset into an XML document.
	*/
	public function dumpDataToXml()
	{
		$data = $this->fetchAll();
		$xml = RivetyCore_Api::makeXml($data->toArray(), $this->_name);
		if (count($data) > 0)
		{
			dd($data);
		}
		return $xml;
	}

	/*
		Function: dumpDataToJson
			Returns a JSON string. Decoding the JSON string yields a nested associative array
			containing the table name, the column names, and all table data after a given date.

		Arguments:
			since_date_field_name - A database column name. This column name will be used in the where clause which limits results by the since_date argument.
			since_date - A datetime-compatible value. Only records on or after that date will be retrieved.
	*/
	function dumpDataToJson($since_date_field_name = null, $since_date = null)
	{
		$where = '';
		if ($since_date_field_name != null && $since_date != null)
		{
			$where = $this->getAdapter()->quoteInto('`' . $since_date_field_name . '` >= ?', $since_date);
		}
		$dataArray = $this->fetchAllArray($where);
		$array_to_encode = array(
			'tableName' => $this->info(Zend_Db_Table_Abstract::NAME),
			'columnNames' => $this->info(Zend_Db_Table_Abstract::COLS),
			'dataRows' => $dataArray,
		);
		return json_encode($array_to_encode);
	}

	/*
		Function: fetchRowArray

		Arguments:
			where - A where clause to search by.

		Returns: array or null
	*/
	function fetchRowArray($where)
	{
		$tmp_row = $this->fetchRow($where);
		if (!is_null($tmp_row))
		{
			$tmp_row = $tmp_row->toArray();
			return $tmp_row;
		}
		else
		{
			return null;
		}
	}

	/*
		Function: findArray
			Fetches one row from the database using an ID.
			Pass an array for composite primary keys.

		Arguments:
			id - The ID of the row to find.

		Returns: one row as an array or null

		About: See Also
			- Zend_Db_Table::find()
	*/
	function findArray()
	{
		$command = "\$tmp_row = \$this->find(";
		$args = func_get_args();
		for ($i = 0; $i < count($args); $i++)
		{
			$command .= "\"".$args[$i]."\"";
			if ($i + 1 < count($args))
			{
				$command .= ", ";
			}
		}
		$command .= ");";
		eval($command);
		if (!is_null($tmp_row) && count($tmp_row) >= 1)
		{
			$tmp_row = $tmp_row->toArray();
			return $tmp_row[0];
		}
		else
		{
			return null;
		}
	}

	/*
		Function: fetchAllArray
			Performs a fetchAll but always returns an array.

		Arguments: TBD

		Returns: multidimensional array of data, or an empty array
	*/
	function fetchAllArray($where = null, $order = null, $limit = null, $page = null)
	{
		$tmp_rows = $this->fetchAll($where, $order, $limit, $page);
		if (!is_null($tmp_rows))
		{
			return $tmp_rows->toArray();
		}
		else
		{
			return array();
		}
	}

	/*
		Function: fetchAllArrayByKeywords
			Search a table with multiple keywords.

		Arguments:
			keywords - A string containing a space-delimited list of words to search for.

		Returns: array of records or an empty array
	*/
	function fetchAllArrayByKeywords($keywords)
	{
		$where = $this->getWhereClauseForKeywords($keywords, $fieldnames);
		return $this->fetchAllArray($where);
	}

	/*
		Function: getWhereClauseForKeywords
			A model-agnostic way to get a where clause to search a table with multiple keywords.

		Arguments:
			keywords - A string containing a space-delimited list of words to search for.

		Returns: string
	*/
	function getWhereClauseForKeywords($keywords)
	{
		$keywords = split(" ", $keywords);
		$where = "1 = 1";
		foreach ($keywords as $word)
		{
			$where .= " and (0 = 1";
			foreach ($this->_keyword_search_field_names as $field)
			{
				$where .= $this->getAdapter()->quoteInto(' or ' . $field . ' like ?', "%" . $word . "%");
			}
			$where .= ")";
		}
		return $where;
	}

	/*
		Function: deleteById
			Deletes one row using an ID, assuming the table's ID is a single column named "id".

		Arguments:
			id - The ID of the row to delete.

		Returns: void
	*/
	function deleteById($id)
	{
		$where = $this->getAdapter()->quoteInto("id = ?", $id);
		$this->delete($where);
	}

	/* Group: Private or Protected Methods */

	/*
		Function: _T
			Translate a string using the module's language files.
	*/
	protected function _T($key, $replace = null)
	{
		return RivetyCore_Translate::translate($this->locale_code, 'default', $key, $replace);
	}

	/*
		Function: fetchAll
	*/
	public function fetchAll($where = null, $order = null, $count = null, $offset = null)
	{
		$result = parent::fetchAll($where, $order, $count, $offset);
		return $result;
	}

	/*
		Function: getBulk

		Arguments:
			ids - An array of IDs.

		Returns: array of row data or an empty array
	*/
	function getBulk(array $ids, $id_field_name = 'id')
	{
		$where = $id_field_name." in (0";
		foreach ($ids as $id)
		{
			$where .= ", ".$id;
		}
		$where .= ")";
		// $where should end up looking like this:
		// id in (0, 4, 23, 6, 324)
		$tmp_rows = $this->fetchAll($where);
		if (!is_null($tmp_rows)) return $tmp_rows->toArray();
		else return array();
	}

	/*
		Function: fetchAllAsSmartyHtmlOptionsArray
	*/
	function fetchAllAsSmartyHtmlOptionsArray($id_field_name = 'id', $label_field_name = 'name')
	{
		$record_array = $this->fetchAllArray();
		$html_options = array('' => 'None');
		foreach ($record_array as $record)
		{
			$html_options[$record[$id_field_name]] = $record[$label_field_name];
		}
		return $html_options;
	}

}
