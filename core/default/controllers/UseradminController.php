<?php

/*
	Class: Useradmin

	About: Author
		Jaybill McCarthy

	About: Contributors
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>
		<RivetyCore_Controller_Action_Admin>
*/
class UseradminController extends RivetyCore_Controller_Action_Admin
{

	/* Group: Actions */

	/*
		Function: index
			The user administration landing page.

		HTTP GET or POST Parameters:
			page - The number of the current page in the listing of users.
			searchterm - A string with a term or terms to search for users with.

		Registry Values:
			useradmin_users_per_page - An integer for the number of users to show per admin page.

		Plugin Hooks:
			- *useradmin_index_pre_select* (filter) - Allows you to affect query being submitted to return a list of users.
				param request - RivetyCore_Request object containing parsed request
				param whereclause - the where clause that will be passed to the user select statement

			- *useradmin_index_post_select* (filter) - Allows you to affect the current array of users before displaying it on the page.
				param users - The array of users to affect.
				param request - RivetyCore_Request object containing parsed request



		View Variables:
			searchterm - The string submitted as a user list search term.
			users - The array of users to display for the current page.
	*/
	function indexAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$users_table = new Users();

		$searchterm = "";
		$whereclause = null;
		if ($request->has('searchterm') && trim($request->searchterm) != "") {
			$searchterm = $request->searchterm;
			if ($this->_request->isPost()) {
				$this->_redirect("/default/useradmin/index/searchterm/".urlencode($searchterm)."/");
			}
			$whereclause = $users_table->getWhereClauseForKeywords($searchterm);
		}
		$this->view->searchterm = $searchterm;

		if ($request->has('role_id')) {
			$this->view->current_role = $request->role_id;
		}

		$roles = new Roles();
		$this->view->roles = $roles->fetchAll()->toArray();

		$params = array('request' => $request, 'whereclause' => $whereclause);
		$params = $this->_rivety_plugin->doFilter($this->_mca . "_pre_select", $params); // FILTER HOOK
		$whereclause = $params['whereclause'];


		$params = array();
		$in_params = $this->_request->getParams();
		$ignore = array('controller', 'action', 'module', 'page');
		foreach ($in_params as $key => $val) {
			if (!in_array($key, $ignore)) {
				if (strlen($val) > 0) {
					$params[$key] = $val;
				}
			}
		}

		$per_page = RivetyCore_Registry::get('useradmin_users_per_page');
		$page = $this->_request->getParam('page', 0);
		$total = $users_table->getCountByWhereClause($whereclause);
		$url = "/default/useradmin/index";

		$this->makePager($page, $per_page, $total, $url, $params);
		$users = $users_table->fetchAllArray($whereclause, 'username asc', $per_page, $per_page * $page);

		$params = array('users' => $users, 'request' => $request, 'view_variables' => array());
		$params = $this->_rivety_plugin->doFilter($this->_mca . "_post_select", $params); // FILTER HOOK
		$users = $params['users'];
		foreach ($params['view_variables'] as $key => $value) {
			$this->view->$key = $value;
		}

		if (count($users) > 0) {
			$this->view->users = $users;
		}
	}

	/*
		Function: edit
			The page for administrators to edit users.
			Either displays an edit form, or processes the form, depending on whether the HTTP Request is GET or POST.
			If the username is invalid, the browser is redirected to '/auth/missing' (currently hardcoded).

		HTTP GET or POST Parameters:
			aboutme - description text for this user
			Birthday_Day - The user's birth day.
			Birthday_Month - The user's birth month.
			Birthday_Year - The user's birth year.
			country_code - The user's country code.
			email - The user's email address.
			gender - User's gender, if chosen
			newpassword - if both this and confirm are set and match, password will be changed
			confirm - see newpassword
			role_ids - array of role ids of the user being edited
			tags - string containing comma seperated tag list
			username - username of user being editied

		Plugin Hooks:
			- *useradmin_edit_pre_render* (filter) - Allows you to affect the user before the page is displayed. Commonly used for adding custom attributes.
				param user - The user to be affected.
				param request - The entire HTTP Request.
			- *useradmin_edit_pre_save* (action) - Allows you to affect the user before it is saved to the database.

		View Variabes:
			countries - And array of all countries pulled from the database.
			end_year - the last year a birthday can have. uses the minimum age param in the registry
			errors - An array of error messages. Only exists if errors occurred.
			genders - array of gender options
			params - All params in the param arrays for filters get turned into view variables automatically.
			roles - array of roles this user could have
			success - success message, if there is one
			errors - array of errors, if there are any
			tags - comma separated list of tags for this user
			user - The user object to be edited
	*/
	function editAction()
	{
		$errors = array();
		$users_table = new Users();
		$users_roles_table = new UsersRoles();
		$request = new RivetyCore_Request($this->getRequest());

		$countries_table = new Countries();
		$this->view->countries = $countries_table->getCountriesArray('Choose a country...');

		$roles_table = new Roles();
		$roles = $roles_table->fetchAll(NULL,"shortname ASC");
		$arRoles = array();
		foreach ($roles as $role)
		{
			if (!strpos($role->shortname,"-base"))
			{
				$arRoles[$role->id] = $role->description;
			}
		}
		$this->view->roles = $arRoles;

		$is_new = true;
		$user = array();
		if ($request->has('username'))
		{
			$obUser = $users_table->fetchByUsername($request->username);
			if (!is_null($obUser))
			{
				$is_new = false;
				$user_roles = $users_roles_table->fetchAll($users_roles_table->select()->where("username = ?", $obUser->username));
				if (count($user_roles) > 0)
				{
					$tmp_selected = array();
					foreach ($user_roles as $user_role)
					{
						$tmp_selected[] = $user_role->role_id;
					}
					$this->view->selected_roles = $tmp_selected;
				}
				$user = $obUser->toArray();
			}
		}
		$this->view->is_new = $is_new;

		if ($is_new)
		{
			// defaults for form fields
			$user['username'] = "";
			$user['full_name'] = "";
			$user['aboutme'] = "";
		}

		$pre_render = $this->_rivety_plugin->doFilter($this->_mca."_pre_render", array('user' => $user, 'request' => $this->_request)); // FILTER HOOK
		$user = $pre_render['user'];

		foreach ($pre_render as $key => $value)
		{
			if ($key != "user")
			{
				$this->view->$key = $value;
			}
		}

		// $tags = unserialize($user['tags']);

		if ($this->getRequest()->isPost())
		{
			$errors = array();

			$request->stripTags(array('full_name', 'email', 'newpassword', 'confirm'));
			// $request->stripTags(array('full_name', 'email', 'newpassword', 'confirm', 'aboutme'));
			$user['username'] = $request->username;
			$user['email'] = $request->email;
			$user['password'] = $request->newpassword;
			$user['confirm'] = $request->confirm;
			$user['full_name'] = $request->full_name;
			$user['birthday'] = $birthday = strtotime($request->Birthday_Day.$request->Birthday_Month.$request->Birthday_Year);
			$user['gender'] = $request->gender;
			$user['country_code'] = $request->country_code;
			$user['aboutme'] = $request->aboutme;

			// validate username
			$username_validator = new Zend_Validate();
			$username_validator->addValidator(new Zend_Validate_StringLength(1, RivetyCore_Registry::get('username_length')));
			$username_validator->addValidator(new Zend_Validate_Alnum());
			if (!$username_validator->isValid($user['username']))
			{
				$show_username = "'".$user['username']."'";
				if (trim($user['username']) == "")
				{
					$show_username = "[".$this->_T("empty")."]";
				}
				$errors[] = $this->_T("%s isn't a valid username. (Between %d and %d characters, only letters and numbers)", array($show_username, 1, RivetyCore_Registry::get('username_length')));
			}
			if ($is_new)
			{
				$user_where = $users_table->getAdapter()->quoteInto('username = ?', $user['username']);
				if ($users_table->getCountByWhereClause($user_where) > 0)
				{
					$errors[] = $this->_T("The username '%s' is already in use",$user['username']);
				}
			}

			// validate email
			if (!RivetyCore_Validate::checkEmail($user['email']))
			{
				$errors[] = $this->_T("Email is not valid");
		 	}

			// check to see if email is in use already by someone else
			if ($users_table->isEmailInUse($user['email'], $user['username']))
			{
				$errors[] = $this->_T("Email already in use");
			}

			// if password isn't blank, validate it
			if ($user['password'] != "")
			{
				if (!RivetyCore_Validate::checkLength($user['password'], 6, RivetyCore_Registry::get('password_length')))
				{
					$errors[] = $this->_T("Password must be between 6 and 32 characters");
		 		}
				// if password is set, make sure it matches confirm
				if ($user['password'] != $user['confirm'])
				{
					$errors[] = $this->_T("Passwords don't match");
				}
			}

			// convert birthday_ts to mysql date
			$birthday = date("Y-m-d H:i:s", $user['birthday']);

			$params = array(
				'request' => $request,
				'user' => $user,
				'errors' => $errors,
			);

			// upload new avatar image if present
			if (array_key_exists('filedata', $_FILES))
			{
				if ($_FILES['filedata']['tmp_name'] != '')
				{
					$destination_path = RivetyCore_Registry::get('upload_path') . "/" . $user['username'] . "/original";
					if (!is_dir($destination_path))
					{
						mkdir($destination_path, 0777, true);
						RivetyCore_Log::report("Creating user folder at " . $destination_path, null, Zend_Log::DEBUG);
					}
					if (file_exists($destination_path . "/avatar"))
					{
						unlink($destination_path . "/avatar");
						RivetyCore_Log::report("Deleted existing user avatar from " . $destination_path, null, Zend_Log::DEBUG);
					}
					else
					{
						RivetyCore_Log::report("User avatar did not exist in " . $destination_path, null, Zend_Log::DEBUG);
					}
					move_uploaded_file($_FILES['filedata']['tmp_name'], $destination_path . "/avatar");
					Users::clearUserCache($user['username']);
					RivetyCore_Log::report("User avatar uploaded to " . $destination_path, null, Zend_Log::DEBUG);
					$params['user']['hasnewfile'] = true;
				}
				else
				{
					$params['user']['hasnewfile'] = false;
				}
			}

			$additional = $this->_rivety_plugin->doFilter($this->_mca . "_pre_save", $params); // FILTER HOOK
			$errors = $additional['errors'];
			$user = $additional['user'];

			$users_roles_table->delete($users_roles_table->getAdapter()->quoteInto("username = ?", $user['username']));
			foreach ($request->role_ids as $role_id)
			{
				$role_data = array("username" => $user['username'], "role_id" => $role_id);
				$users_roles_table->insert($role_data);
			}

			if (count($errors) == 0)
			{
				/**********  Commented out due to Plug-in compatibility issues.
				$data = array(
					'email' => $user['email'],
					'birthday' => $birthday,
					'aboutme' => nl2br($user['aboutme']),
					'gender' => $user['gender'],
					'full_name' => $user['full_name'],
					'country_code' => $user['country_code'],
					'last_modified_on' => date(DB_DATETIME_FORMAT),
				);
				**********/

				$user['birthday'] = $birthday;
				$user['aboutme'] = nl2br($user['aboutme']);
				$user['last_modified_on'] = date(DB_DATETIME_FORMAT);

				// This is a hold-over value from the form.
				unset($user['confirm']);

				if ($user['password'] != "")
				{
					#$data['password'] = $user['password'];
				}
				else
				{
					unset($user['password']);
				}

				if ($is_new)
				{
					$filter_hook_params = array(
						'request' => $request,
						'user' => $user,
						'errors' => $errors,
					);
					$additional1 = $this->_rivety_plugin->doFilter($this->_mca, $filter_hook_params); // FILTER HOOK
					$errors = $additional1['errors'];
					$user = $additional1['user'];
					// $data['username'] = $user['username'];
					// $data['created_on'] = date(DB_DATETIME_FORMAT);
					$user['created_on'] = date(DB_DATETIME_FORMAT);
					$users_table->insert($user);
					$this->view->success = "Profile created.";
				}
				else
				{
					$where = $users_table->getAdapter()->quoteInto('username = ?', $user['username']);
					// $users_table->update($data, $where);
					$users_table->update($user, $where);

					$this->view->success = "Profile updated.";
				}
			}
			else
			{
				$this->view->errors = $errors;
			}
		}
		$this->view->end_year = -(RivetyCore_Registry::get('minimum_registration_age'));
		$this->view->genders = RivetyCore_Common::getGenderArray();
		$user['aboutme'] = RivetyCore_Common::br2nl($user['aboutme']);
		$this->view->user = $user;
	}

	/*
		Function: delete
			Deletes a user. After a successful deletion, the browser is redirected to '/useradmin' (currently hardcoded).

		HTTP POST Parameters:
			del - If the value is yes, the user will be deleted, otherwise the browser will just be redirected to '/useradmin' (currently hardcoded).

		Plugin Hooks:
			- *useradmin_delete_pre_delete* (filter) - Allows you to change whether or not the user's row is actually deleted from the database.
				This is useful if you want to, for instance, mark users as deleted instead of actually deleting all their data.
				param username - The username of the user to be deleted.
				param delete_row - A boolean to determine whether the user's row in the database should be deleted or not. Defaults to true.
			- *useradmin_delete_post_delete* (action) - Allows you to trigger actions after the user has been deleted.

		View Variables:
			pagetitle - The HTML page title.
			user - The user to delete.
	*/
	function deleteAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$users_table = new Users();
		$username = $request->username;
		if ($this->getRequest()->isPost()) {
			$del = strtolower($request->delete);
			if ($del == 'yes' && !is_null($username)) {
				$params = array('username' => $username, 'delete_row' => true);
				$params = $this->_rivety_plugin->doFilter($this->_mca . "_pre_delete", $params); // FILTER HOOK
				if ($params['delete_row']) {
					$where = $users_table->getAdapter()->quoteInto('username = ?', $username);
					$users_table->delete($where);
				}
				$this->_rivety_plugin->doAction($this->_mca . "_post_delete", $params); // ACTION HOOK
				$this->view->success = "User '".$username."' has been deleted.";
				$this->view->username = $username;
			} else {
				$this->_redirect('/default/useradmin/index');
			}
		} else {
			$this->view->notice = "Warning: You are about to delete user '".$username."'. This cannot be undone.";
			if (!is_null($username)) {
				$user = $users_table->fetchByUsername($username);
				if (!is_null($user)) {
					$this->view->user = $user->toArray();
					$this->view->username = $user->username;
				} else {
					$this->_redirect('/default/useradmin/index');
				}
			}
		}
	}

	/*
		Function: testdata
			Adds a bunch of test users with random but plausible data.

		Registry Values:
			test_data_path - if set, will be used as a default param for where to look for test data

		Plugin Hooks:
			- *useradmin_testdata_precommit* (filter) - fires after the test user data is created but before
				saving it to the db. Fires once for each user. Useful for plugins that want to add additional
				uaser data or modify the sample data as it's created. CURRENTLY BROKEN.

				param user - the array containing the user data.
	*/
	function testdataAction()
	{
		$request = new RivetyCore_Request($this->getRequest());

		if ($this->getRequest()->isPost()) {
			$errors = array();
			$data_path = $request->data_path;
			$data_file = $data_path . "/users.dat";

			$image_dir = $data_path . "/images";
			$users_table = new Users();
			$users_roles_table = new UsersRoles();

			if($request->has("email_domain")){
				$email_domain = $request->email_domain;
			} else {
				$email_domain = "nowhere.com";
			}

			if (!file_exists($data_file)) {
				$errors[] = $this->_T("Data file missing. Check path.");
			} else {
				$users = unserialize(file_get_contents($data_file));
				if (!is_array($users)) {
					$errors[] = $this->_T("Data file is corrupt or something.");
				}
			}

			if (count($errors) == 0) {

				$old_users = $users_table->fetchAll();

				foreach ($old_users as $old_user) {
					if ($users_table->getMetaData($old_user->username, "is_test_user") == "true") {
						$where = $users_table->getAdapter()->quoteInto("username = ?", $old_user->username);
						$users_table->delete($where);
						$users_roles_table->delete($where);
					}
				}

				$count = 0;
				foreach ($users as $user) {
					$tmp_user = array();
					foreach ($user as $key => $value) {
						if ($key != "avatar") {
							$tmp_user[$key] = $value;
						}
					}

					$tmp_user['email'] = strtolower($tmp_user['username'] . "@" . $email_domain);
					$tmp_user['password'] = "password";

					$destination_path = $users_table->getAvatarPath($user['username']);
					$destination_filename = $users_table->getAvatarPath($user['username'], true);
					if (!is_dir($destination_path)) {
						mkdir($destination_path, 0777, true);
					}
					if (file_exists($destination_filename)) {
						unlink($destination_filename);
					}

					$source_image = $image_dir."/".$user['avatar'];
					copy($source_image, $destination_filename);
					$role_data = array("username" => $tmp_user['username'],"role_id" => $tmp_user['role_id']);
					$users_roles_table->insert($role_data);
					unset($tmp_user['role_id']);
					$users_table->insert($tmp_user);
					$users_table->setMetaData($tmp_user['username'], "is_test_user", "true");
					$save_users[] = $user;
					$count++;
				}
				$this->view->success = "User data loaded. Created ".$count." users.";
				RivetyCore_Registry::set('test_data_path', $request->data_path);
				$this->view->data_path = RivetyCore_Registry::get('test_data_path');
				$this->view->email_domain = $email_domain;
			} else {
				$this->view->errors = $errors;
				$this->view->data_path = Zend_Registry::get('basepath')."/tmp/testdata";
				$this->view->email_domain = $request->email_domain;
			}
		} else {
			$this->view->data_path = Zend_Registry::get('basepath')."/tmp/testdata";
			$this->view->email_domain = "nowhere.com";
			$this->view->notice = $this->_T("Warning: If you are reinstalling the test data, the old test data will be overwritten. Users created outside the test data should not be affected.");
		}
	}

}
