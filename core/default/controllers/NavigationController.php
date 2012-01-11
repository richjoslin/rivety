<?php

/*
	Class: Navigation

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>
		<RivetyCore_Controller_Action_Admin>
*/
class NavigationController extends RivetyCore_Controller_Action_Admin
{

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			Initializes the current instance.
			Initializes the parent object (calls init() on the parent instance).
	*/
	function init()
	{
		parent::init();
	}

	/* Group: Actions */

	/*
		Function: edit
			Allows editing of a nav link for a certain role.
			If no ID is passed in, it is used to create a new link.
	*/
	function editAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		if ($request->has('role_id'))
		{
			$nav_id = $request->nav_id;
			$role_id = $request->role_id;
			$nav_table = new Navigation($role_id, $this->locale_code);
			$roles_table = new Roles();
			$role = $roles_table->fetchRow("id = " . $role_id);
			if (!is_null($role))
			{
				$role = $role->toArray();
				$this->view->role = $role;
			}
			else
			{
				die("Invalid role.");
			}
			if ($this->getRequest()->isPost())
			{

				$parent_id = $request->parent_id;
				$short_name = $request->short_name;
				$link_text = $request->link_text;
				$url = $request->url;
				$data = array(
					'parent_id' => $parent_id,
					'short_name' => $short_name,
					'link_text' => $link_text,
					'url' => $url,
				);
				if ($nav_id == 0)
				{
					// INSERT
					$data['role_id'] = $role_id;
					// make sure it's the last item
					$data['sort_order'] = '99999999999';
					$params = array($role_id,$this->locale_code);
					$nav_table->insert($data);
					// now get rid of all those nines
					RivetyCore_Sort::reNumber('Navigation', "parent_id = " . $parent_id . " and role_id = " . $role_id, 'id', 'sort_order', 10, $params);
				}
				else
				{
					// UPDATE
					$nav_table->update($data, 'id = ' . $nav_id);
				}
				RivetyCore_Cache::removeByTags(array('navigation'));
				$this->_redirect('/default/navigation/editrole/id/' . $role_id);
			}
			else
			{
				$nav_s = new Navigation($role_id,$this->locale_code);
				$new_nav = $nav_s->getNavTree();
				$this->view->parent_choices = $new_nav;
				$this->view->role_id = $role_id;
				if ($nav_id == 0)
				{
					// CREATE
					$this->view->pagetitle = 'Create Nav Link for ' . $role['shortname'] . ' Role';
					$this->view->nav_id = '0';
					$this->view->parent_id = '0';
					$this->view->short_name = '';
					$this->view->link_text = '';
					$this->view->url = '';
				}
				else
				{
					// EDIT
					$nav_item_temp = $nav_table->fetchRow("id = " . $nav_id);
					if (!is_null($nav_item_temp))
					{
						$this->view->pagetitle = "Edit Nav Link for " . $role["shortname"] . " Role";
						$this->view->nav_id = $nav_id;
						$this->view->parent_id = $nav_item_temp->parent_id;
						$this->view->short_name = $nav_item_temp->short_name;
						$this->view->link_text = $nav_item_temp->link_text;
						$this->view->url = $nav_item_temp->url;
					}
					else
					{
						$this->_forward('default', 'auth', 'missing'); return;
					}
				}
			}
		}
		else
		{
			$this->_forward('default', 'auth', 'missing'); return;
		}
	}

	/*
		Function: editrole
			Edit a role's set of hardwired navigation links.
			This is not controlling permissions and is not linked to the permissions framework at all.
			Just a list of links for each role.

		URL Parameters:
			id - The ID of the role being edited.

		View Variables:
			nav_items - An array of all nav items (full tree), not just the ones to which this role has access.
	*/
	function editroleAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		if ($request->has('id'))
		{
			$role_id = $request->id;
			$nav_table = new Navigation($role_id,$this->locale_code);
			$roles_table = new Roles();
			$role = $roles_table->fetchRow("id = " . $role_id);
			if (!is_null($role)) $this->view->role = $role->toArray();
			else die("Invalid role.");
			// nav_items is already used in the main admin nav
			$this->view->nav_items_to_edit = $nav_table->getNavTree();
		}
		else
		{
			$this->_forward('default', 'auth', 'missing'); return;
		}
	}

	/*
		Function: delete
			Delete a nav link for a certain role.
			After a successful deletion, the browser is redirected to '/role/' (currently hardcoded).

		HTTP POST Parameters:
			delete - Value must be yes or this doesn't work.
			nav_id - The id of the link to delete.
			role_id - The id of the role we're editing (mainly for redirect purposes).

		View Variables:
			nav_id - The ID of the link to delete from the default_navigation table.
			pagetitle - The title to put in the H1 header.
			role_id - The id of the role we're editing (mainly for redirect purposes).
	*/
	function deleteAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$role_id = $request->role_id;
		$nav_table = new Navigation($role_id,$this->locale_code);
		$nav_id = (int)$request->nav_id;
		$nav = $nav_table->fetchRow($nav_table->getAdapter()->quoteInto("id = ?", $nav_id));
		if ($request->has("nav_id") && $request->has("role_id") && !is_null($nav))
		{
			$this->view->nav = $nav->toArray();
			if ($nav_table->hasChildren($nav_id))
			{
				$this->view->can_delete = false;
				$this->view->notice = 'Sorry, you cannot delete a link that has children.';
			}
			else
			{
				$this->view->can_delete = true;
				if ($this->getRequest()->isPost())
				{
					$delete = trim(strtolower($this->_request->getPost('delete')));
					if ($delete == 'yes' && $nav_id > 0)
					{
						$nav_table->delete('id = ' . $nav_id);
					}
					RivetyCore_Cache::removeByTags(array('navigation'));
					$this->_redirect('/default/navigation/editrole/id/' . $role_id . '/');
				}
			}
			$this->view->nav_id = $nav_id;
			$this->view->role_id = $role_id;
		}
		else
		{
			$this->_forward('default', 'auth', 'missing'); return;
		}
	}

	/*
		Function: moveup
			Adjusts the sort order values in a particular group of nav
			items so the specified item is moved up one higher in the order.

		URL Parameters:
			nav_id - The ID of the navigation item to move.
			parent_id - The ID of the parent of the navigation item to move.
			role_id - The ID of the role being edited.
	*/
	function moveupAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$this->move($request, -15);
	}

	/*
		Function: moveup
			Adjusts the sort order values in a particular group of nav
			items so the specified item is moved down one lower in the order.

		URL Parameters:
			nav_id - The ID of the navigation item to move.
			parent_id - The ID of the parent of the navigation item to move.
			role_id - The ID of the role being edited.
	*/
	function movedownAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$this->move($request, 15);
	}

	/* Group: Private or Protected Methods */

	/*
		Function: move
			Used by <moveup> and <movedown> in adjusting the sort values for navigation items.

		Arguments:
			request - An HTTP Rquest object with a GET request containing the nav_id and role_id URL parameters and optional parent_id URL parameter.
			adjustment - An integer to use to add or subtract to the current position of the navigation item.
	*/
	private function move($request, $adjustment)
	{
		if ($request->has('nav_id') && $request->has('role_id'))
		{
			$nav_id = $request->nav_id;
			$role_id = $request->role_id;
			$class_name = "Navigation";
			$params = array($role_id, $this->locale_code);
			RivetyCore_Sort::adjustSortValue($class_name, $nav_id, $adjustment, 'id', 'sort_order', $params);
			$where_clause = $request->has('parent_id') ? "parent_id = " . $request->parent_id . " and role_id = " . $role_id : "parent_id = 0 and role_id = " . $role_id;
			RivetyCore_Sort::reNumber($class_name, "parent_id = " . $request->parent_id . " and role_id = " . $role_id, 'id', 'sort_order', 10, $params);
			RivetyCore_Cache::removeByTags(array('navigation'));
			$this->_redirect("/default/navigation/editrole/id/" . $role_id);
		}
		else
		{
			$this->_redirect("/default/auth/missing");
		}
	}

}
