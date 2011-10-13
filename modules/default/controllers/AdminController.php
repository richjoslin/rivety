<?php

/*
	Class: Admin

	About: Author
		Jaybill McCarthy

	About: Contributors
		Rich Joslin

	About: License
		<http://communit.as/docs/license>

	About: See Also
		<Cts_Controller_Action_Abstract>
		<Cts_Controller_Action_Admin>
*/
class AdminController extends  Cts_Controller_Action_Admin
{

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			For this class, does nothing other than initialize the parent object (calls init() on the parent instance).
	*/
	function init()
	{
        parent::init();
    }

	/* Group: Actions */

	/*
		Function: index
			The administration landing page. Currently does nothing. Template might contain welcome text.

		Plugin Hooks:
			- *default_admin_index* (filter) - Used to add content to the admin index page. Any key that gets set in the params array will be sent to the view with that name.
				param username - The username of the current user if they are logged in, or null otherwise.
	*/
    function indexAction()
	{
		$params = array('username' => $this->_identity->username);
		$additional = $this->_cts_plugin->doFilter($this->_mca, $params); // FILTER HOOK
		foreach ($additional as $key => $value)
		{
			$this->view->$key = $value;
		}

		// // FOR TESTING
		// $last_publish_datetime = '2011-10-08';
		// $user_model = new PagepartsPages();
		// dd($user_model->dumpDataToJson('last_modified_on', $last_publish_datetime));
    }

}
