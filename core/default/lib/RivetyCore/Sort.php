<?php

/*
	Class: RivetyCore_Sort

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_Sort
{

	/* Group: Static Methods */

	static function adjustSortValue($class_name, $id_column_value, $adjustment, $id_column_name = 'id', $sort_column_name = 'sort_order', $params = null)
	{
		if (is_array($params)) $params = implode(",", $params);
		$table = new $class_name($params);
		$where = $table->getAdapter()->quoteInto($id_column_name . ' = ?', $id_column_value);
		$item_temp = $table->fetchRow($where);
		if (!is_null($item_temp))
		{
			$new_sort_order = $item_temp->sort_order + $adjustment;
			$table->update(array($sort_column_name => $new_sort_order), $where);
		}
	}

	static function reNumber($class_name, $where = '', $id_column_name = 'id', $sort_column_name = 'sort_order', $multiplier = 10, $params = null)
	{
		if (is_array($params)) $params = implode(",",$params);
		
		$table = new $class_name($params);
		$items_temp = $table->fetchAll($where, $sort_column_name);
		if (!is_null($items_temp))
		{
			$items_temp = $items_temp->toArray();
			$sort_values = array();
			foreach ($items_temp as $item_temp)
			{
				$new_sort_order = $item_temp[$sort_column_name];
				// the sort order value should be first
				// that way a simple sort() is all we need to re-sort the array properly
				$sort_values[] = array($sort_column_name => $new_sort_order, $id_column_name => $item_temp[$id_column_name]);
			}
			// re-sort the items
			sort($sort_values);
			// re-number the items to lock in the new sort order
			for ($i = 1; $i <= count($sort_values); $i++)
			{
				$sort_values[$i - 1][$sort_column_name] = $i * $multiplier;
			}
			// update the database for each record with the new sort order
			foreach ($sort_values as $sort_value) {
				$where = $table->getAdapter()->quoteInto($id_column_name . ' = ?', $sort_value[$id_column_name]);

				$table->update(array($sort_column_name => $sort_value[$sort_column_name]), $where);
			}
		} else {
			die('Where clause returned no results when trying to re-number items.');
		}
	}

}
