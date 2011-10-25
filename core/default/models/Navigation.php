<?php

/*
	Class: Navigation

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	About: See Also
	 	- <RivetyCore_Db_Table_Abstract>
*/
class Navigation extends RivetyCore_Db_Table_Abstract
{

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
	protected $_name = 'default_navigation';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
	protected $_primary = 'id';

	/*
		Variable: $role_id
	*/
	public $role_id;

	/*
		Variable: $locale_code
	*/
	public $locale_code;

	/* Group: Constructors */

	/*
		Constructor: __construct
			Calls the parent's constructor and sets the private role_id variable.
			Role ID is required in order to create a Navigation object.

		Arguments:
			role_id - None of these methods will work without a role ID.
			config (optional) - Required by parent constructor
	*/
	// function __construct($params = null) //, $role_id, $locale_code = "en-us", $config = null, $restricted = null)
	function __construct($role_id, $locale_code = "en-us", $config = null, $restricted = null)
	{
		// TODO: finish changing this into a params array being passed in
		// $this->role_id = $params['role_id'];
		// $this->locale_code = $params['locale_code'];

		$this->role_id = $role_id;
		$this->locale_code = $locale_code;

		if (is_array($this->role_id))
		{
			$all_roles = $this->role_id;
		}
		else
		{
			$all_roles = array($this->role_id);
		}
  		$roles_table = new Roles();
		foreach ($all_roles as $role)
		{
			$all_roles = array_merge($all_roles, $roles_table->getAllAncestors($role));
		}
		$this->all_roles = array_unique($all_roles);
		return parent::__construct($config);
	}

	/* Group: Instance Methods */

	/*
		Function: getNavTree
			Returns a full tree (very jagged multidimensional array) of all navigation items that apply to the given role.

		Returns: array
	*/
	function getNavTree()
	{
		$top_nav_items = $this->getNavArrayByParentId(0);
		return $this->buildRecursiveNav(array(), $top_nav_items);
	}

	/*
		Function: buildRecursiveNav
			This is a recursive function that iterates through the supplied array, figures out if there are children,
			and either continues parsing the children (recursively),
			or assigns the results to the supplied parent (if the current recursion is complete).

		Arguments:
			nav_items - An array of nav items as pulled from the database using <getNavArrayByParentId>.
			parent_item - The item to which to add children if any are found.

		Returns: An array of nav items along with any descendants they have (i.e.: a tree, or multidimensional array).
	*/
	private function buildRecursiveNav($parent_item, $nav_items)
	{
		foreach ($nav_items as $nav_item)
		{
			$child_items = $this->getNavArrayByParentId($nav_item['id']);
			if (!array_key_exists('id', $parent_item))
			{
				$parent_item[$nav_item['short_name']] = $this->buildRecursiveNav($nav_item, $child_items);
			}
			else
			{
				$parent_item['children'][$nav_item['short_name']] = $this->buildRecursiveNav($nav_item, $child_items);
			}
		}
		return $parent_item;
	}

	/*
		Function: getNavArrayByParentId

		Arguments:
			parent_id - The ID of the nav item for which to get children.

		Returns: array of nav items (as pulled from default_navigation table)
	*/
	function getNavArrayByParentId($parent_id)
	{
		$select = $this->select();
		$select->where("parent_id = ?", $parent_id);
		$select->where("role_id in (" . implode($this->all_roles, ",") . ")");
		$select->order('sort_order asc');
		$nav_items = $this->fetchAllArray($select);
		if (!is_null($nav_items))
		{
			$params = array('nav_items' => $nav_items, 'locale_code' => $this->locale_code);
			$params = RivetyCore_Plugin::getInstance()->doFilter('default_nav_filter', $params); // FILTER HOOK
			return $params['nav_items'];
		}
		else
		{
			// return an empty array instead of null so a foreach on the result doesn't throw a warning
			return array();
		}
	}

	/*
		Function: hasChildren
			Determines if a nav item has children.

		Arguments:
			nav_id: The ID of the nav item for which to determine whether it has children.

		Returns: boolean
	*/
	function hasChildren($nav_id)
	{
		return count($this->getNavArrayByParentId($nav_id)) > 0;
	}

}
