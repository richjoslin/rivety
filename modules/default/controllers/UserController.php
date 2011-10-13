<?php

/*
	Class: User

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		<Cts_Controller_Action_Abstract>
*/
class UserController extends Cts_Controller_Action_Abstract {

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			Initializes the current instance.
			Also initializes the parent object (calls init() on the parent instance).

		HTTP GET or POST Parameters:
			username - The username of the user upon which to act. (optional)

		Registry Values:
			full_name_length - The maximum number of characters allowed in a user's full name. Defaults to 50.
			username_length - The maximum number of characters allowed in a username. Default to 16.
	*/
	function init() {
		parent::init();
		$this->view->full_name_length = Cts_Registry::get('full_name_length');
		$this->view->username_length = Cts_Registry::get('username_length');
		$this->_users_table = new Users();
		if ($this->_auth->hasIdentity()) {
			$default = $this->_identity->username;
		} else {
			$default = null;
		}
		$request = $this->getRequest();
		$this->_username = $request->getParam('username', $default);
		$this->_user = $this->_users_table->fetchByUsername($this->_username);
		if (!is_null($this->_user)) {
			$this->view->nav_username = $this->_username;
			$this->view->user = $this->_user->toArray();
		}
	}

	/* Group: Actions */

	/*
		Function: loginbounce
			After a user is logged in, they are redirected to this action.
			It builds a URI with the supplied values and redirects the browser to that URI.

		HTTP GET or POST Parameters:
			backto - TBD
			username - The username of the person logging in.
	*/
	function loginbounceAction() {
		$username = $this->_request->getParam('username');
		$backto = $this->_request->getParam('backto');
		$this->_redirect('/default/user/' . $backto . '/username/' . $username);
	}

	/*
		Function: cancel
			Allows a user to cancel their account. Either displays an input form
			or processes the input form depending on whether the HTTP Request is
			GET or POST. After the cancellation form is processed, the browser
			is redirected to '/auth/logout' (currently hardcoded).

		HTTP POST Parameters (when processing):
			delete - This is a sort of boolean value whose possible values are 'yes' or anything else. The user is only deleted if the value of del is 'yes'.

		Plugin Hooks:
			- *default_user_cancel_pre_delete* (filter) - Occurs before a user removed from the system.
				param delete_row - Do or do not delete the user's row from the database. Defaults to true (do delete the row).
				param username - The username of the user to be deleted. Defaults to the currently logged-in user.
			- *default_user_cancel_post_delete* (action) - Occurs after a user is removed from the system.

		View Variables:
			pagetitle - The value to be displayed in the browser's title bar.
			user - A user object for the user to be deleted.
	*/
	function cancelAction() {

		$users_table = new Users();
		$username = $this->_identity->username;
		if($this->getRequest()->isPost()){
			$del = strtolower($this->_request->getPost('delete'));
			if($del == 'yes' && !is_null($username)){
				$params = array('username' => $username, 'delete_row' => true);
				$params = $this->_cts_plugin->doFilter($this->_mca . "_pre_delete", $params); // FILTER HOOK
				if($params['delete_row']){
					$where = $users_table->getAdapter()->quoteInto('username = ?', $username);
					$users_table->delete($where);
				}
				$this->_cts_plugin->doAction($this->_mca . "_post_delete", $params); // ACTION HOOK
			}
			$this->_redirect('/default/auth/logout');
		}else{
			if(!is_null($username)){
				$user = $users_table->fetchByUsername($username);
				if(!is_null($user)){
					$this->view->user = $user->toArray();
				}else{
					$this->_redirect('/');
				}
			}
		}
	}

	/*
		Function: index
			Displays a list of community members and a search form.

		HTTP GET or POST Parameters:
			gender - For searching/filtering by gender.
			max_age - For searching/filtering by a maximum age selection.
			min_age - For searching/filtering by a minimum age selection.
			page - The index of the current page.
			region - For searching/filtering by region.
			searchterm - For searching/filtering by keyword. Database fields searched: username, full_name, and tags.
			sortby - For changing the sort order of search results. Possible values: 'updated', 'newest', and 'login'. Default is 'newest'.

		Registry Values:
			users_per_page - The number of users to show on each page. Default is 30.

		Plugin Hooks:
			- *default_user_index_users* (filter) - Allows you to act upon the list of users before it is sent to the view.
				param users - The array of users to be displayed.
			- *defaut_user_index_search* (filter) - Allows you to act on the search criteria before searching
				param whereclauses - An array of where clauses that will be passed to the search
				param params - a key/value array of search terms that came in on the request.

		View Variables:
			ages - An array of ages (integers) from 13 to 70 (currently hardcoded).
			countries - An array of countries pulled from the database.
			genders - An array of key-value pairs consisting of a formal and informal names (currently hardcoded) for each gender and an 'any' option.
			pagetitle - The HTML page title.
			params - All params in the param arrays for filters get turned into view variables automatically.
			regions - An array of regions, typically consisting of continents (currently hardcoded).
			signs - An array of astrological star signs generated with Cts_Common::GetSignArray.
			users - An array of users to display as a list of links.
	*/
	function indexAction() {
		$request = new Cts_Request($this->getRequest());
		$users_table = new UsersIndex();

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

		$params = array('request' => $request, 'whereclause' => $whereclause);
		$params = $this->_cts_plugin->doFilter($this->_mca . "_pre_select", $params); // FILTER HOOK
		$whereclause = $params['whereclause'];

		$params = array();
	   	$in_params = $this->_request->getParams();
	   	$ignore = array('controller', 'action', 'module', 'page');
	   	foreach($in_params as $key => $val) {
	   		if (!in_array($key, $ignore)) {
	   			if (strlen($val) > 0) {
	   				$params[$key] = $val;
	   			}
	   		}
	   	}

		// $select = Zend_DB_Table::getDefaultAdapter()->select();
		// $search_filter_params = array(
		// 	"whereclauses" => array(),
		// 	"params" => $params,
		// );
		// $search_filter_results = $this->_cts_plugin->doFilter($this->_mca."_search", $search_filter_params); // FILTER HOOK
		// $whereclauses = $search_filter_results['whereclauses'];
		// foreach ($whereclauses as $whereclause) {
		// 	$select->where($whereclause);
		// }
		// $params = $search_filter_results['params'];
		// 
		// $searchClause = null;
		// 
		// if (array_key_exists('searchterm', $params)) {
		// 	if (!is_null($params['searchterm'])) {
		// 		$searchterms = explode(',', $params['searchterm']);
		// 		$firstpass = true;
		// 		$searchClause = "";
		// 		foreach ($searchterms as $searchterm) {
		// 			if (!$firstpass) {
		// 				$searchClause .= " OR ";
		// 			}
		// 			$searchterm = str_replace(' ', '-', $searchterm);
		// 			$searchClause .= $users_table->getAdapter()->quoteInto('tags like ?', '%"' . $searchterm . '"%');
		// 			$searchClause .= $users_table->getAdapter()->quoteInto('or username like ?', '%' . $searchterm . '%');
		// 			$searchClause .= $users_table->getAdapter()->quoteInto('or full_name like ?', '%' . $searchterm . '%');
		// 			$firstpass = false;
		// 		}
		// 		$searchClause = " (".$searchClause.")";
		// 	}
		// }

		if (array_key_exists('sortby', $params)) {
			switch ($params['sortby']) {
				case "updated":
					$order = "last_modified_on desc";
					break;
				case "newest":
					$order = "created_on desc";
					break;
				case "login":
					$order = "last_login_on desc";
					break;
				default:
					$order = "created_on desc";
					break;
			}
		} else {
			$params['sortby'] = "newest";
			$order = "created_on desc";
		}

		// $where_array = $select->getPart('where');
		// foreach ($where_array as $where_part) {
		// 	$whereclause .= " " . $where_part . " ";
		// }
		// if (!is_null($searchClause)) {
		// 	if (!is_null($whereclause)) {
		// 		$whereclause .= " and " . $searchClause;
		// 	} else {
		// 		$whereclause = 	$searchClause;
		// 	}
		// }

		$this->view->params = $params;

		$per_page = Cts_Registry::get('users_per_page');
		$page = $this->_request->getParam('page', 0);
		$total = $users_table->getCountByWhereClause($whereclause);
		$url = "/default/user/index";

		$this->makePager($page, $per_page, $total, $url, $params);

		$users = $users_table->fetchAllArray($whereclause, $order, $per_page, $per_page * $page);
		$params = array('users' => $users);
		$params = $this->_cts_plugin->doFilter($this->_mca . "_users", $params); // FILTER HOOK
		$users = $params['users'];

		$tmp_users = array();
		if (count($users) > 0) {
			foreach ($users as $user) {
				$user['sign'] = Cts_Common::calculateAstroSign(strtotime($user['birthday']));
				$countries_table = new Countries();
				$where = $countries_table->getAdapter()->quoteInto('country_code = ?', $user['country_code']);
				$country = $countries_table->fetchRow($where);
				if (!is_null($country)) {
					$user['location'] = $country->country;
				}
				$tmp_users[] = $user;
			}
			$this->view->users = $tmp_users;
		}
		$this->view->pagetitle = $this->_T("Browse Profiles");
	}

	/*
		Function: resetpassword
			This is step 2 of 2 in the process a user goes through to reset their password. Either displays an input form, or processes the form.
			The only way to get to this page should be from a link in an automatically-generated email sent to the user (done in the <forgotpassword> action).
			The <_checkConfirmationUrl> method is used to check if the incoming email address and secret code comprise a valid password reset request.
			If there is an active authenticated session, the browser is redirected to '/user/edit' (currently hardcoded).
			Upon successfully resetting the password, the browser is redirected to '/user/postregister' (currently hardcoded).

		URL Parameters (when coming from the email):
			code - The randomly generated secret code contained in the email.
			email - The email address of the user trying to reset their password.

		HTTP POST Parameters (for resetting the password):
			code - The randomly generated secret code contained in the email.
			confirm - The new password a second time.
			ctaspassword - The new password.
			email - The email address of the user trying to reset their password.

		View Variables:
			$code - The randomly generated secret code contained in the email.
			$email - The email address of the user trying to reset their password.

		See Also:
			- <resetpassword>
			- <_checkConfirmationUrl>
	*/
	function resetpasswordAction() {
		$request = new Cts_Request($this->getRequest());
		if ($this->_auth->hasIdentity()) {
			$this->_redirect('/default/user/edit');
		}
		$code = $this->_request->getParam('code', null);
 		$email = $this->_request->getParam('email', null);
		if (!$this->_checkConfirmationUrl($email, $code)) {
			$this->_forward('default', 'auth', 'missing'); return;
		}
		$field_name = Cts_Registry::get('password_reset_field_name');
		$this->view->field_name = $field_name;
		$this->view->code = $code;
		$this->view->email = $email;

		if ($this->_request->isPost()) {
			$users_table = new Users();
			$password = $this->_request->getPost($field_name);
			$confirm = $this->_request->getPost('confirm');
			$errors = array();
			$password_validator = new Zend_Validate();
			$password_validator->addValidator(new Zend_Validate_StringLength(6, 32));
			// make sure password is at least six chars
			if (!$password_validator->isValid($password)){
				$errors[] = $this->_T("Password must be between %d and %d characters",array(6, Cts_Registry::get('password_length')));
			}
			if ($password != $confirm) {
				$errors[] = $this->_T("Passwords don't match");
			}
			$where = $users_table->getAdapter()->quoteInto('email = ?', $email);
			$user = $users_table->fetchRow($where);
			if (is_null($user)) {
			   $errors[] = $this->_T("User does not exist.");
			}
			if (count($errors) == 0) {
				$params = array(
					'request' => $this->getRequest(),
					'user' => $user,
					'password' => $password,
					'errors' => $errors,
				);
				$additional = $this->_cts_plugin->doFilter($this->_mca, $params); // FILTER HOOK
				$errors = $additional['errors'];
				$user = $additional['user'];

				// okay, looks good. change the password and auto login
				$data = array('password' => $password);
				$users_table->update($data, $where);

				// DO SOME PLUGINS
				$params = array(
					'request' => $request,
					'username' => $user->username,
					'autologin' => true,
					'autologin_username' => $user->username,
					'autologin_password' => $password,
					'autologin_password_hash' => md5($password),
				);
				$params = $this->_cts_plugin->doFilter("default_user_resetpassword_done", $params); // FILTER HOOK

				// SET UP AUTO-LOGIN, OR DON'T
				if ($params['autologin']) {
					$appNamespace = new Zend_Session_Namespace('Cts_Temp');
					$appNamespace->autoLogin = $params['autologin'];
					$appNamespace->autoLoginUsername = $params['autologin_username'];
					$appNamespace->autoLoginPassword = $params['autologin_password'];
					$appNamespace->autoLoginPasswordHash = $params['autologin_password_hash'];
				}

				// SEND THE USER ON THEIR WAY
				$this->_redirect('/default/user/postregister');

			} else {
				$this->view->errors = $errors;
			}
		}
	}

	/*
		Function: forgotpassword
			This is step 1 of 2 in the process a user goes through to reset their password.
			Either displays an input form, or processes the form, depending on whether the HTTP Request is GET or POST.
			Uses <_getConfirmationUrl> to build a return URL for the email complete with a random secret code.
			If there is an active authenticated session, the browser is redirected to the logged-in user's profile (currently hardcoded).

		HTTP POST Parameters:
			username - The username of the user trying to reset their password.
			email - The email address of the user trying to reset their password.

		View Variables:
			$email - The user's email address. Only sent to the view if there are errors.
			$errors - An array of error messages. Only sent to the view if there are errors.
			$pagetitle - The HTML title for the page.
			$showForm - A boolean to determine whether to show the input form or not.
			$success - A string containing a message to display upon success (currently hardcoded).
			$username - The user's username. Only sent to the view if there are errors.

		See Also:
			- <resetpassword>
			- <_getConfirmationUrl>
	*/
	function forgotpasswordAction() {
		if ($this->_auth->hasIdentity()) {
			$this->_redirect('/default/user/profile/username/'.$this->_identity->username);
		}
		$this->view->pagetitle = "Forgot Password";
		if ($this->_request->isPost()) {
			$username = trim($this->_request->getPost('username'));
			$email = trim($this->_request->getPost('email'));
			$errors = array();
			$users_table = new Users();
			$username_where = $users_table->getAdapter()->quoteInto('username = ?', $username);
			$test_user = $users_table->fetchRow($username_where);
			if (is_null($test_user)) {
				$email_where = $users_table->getAdapter()->quoteInto('email = ?', $email);
				$test_user = $users_table->fetchRow($email_where);
				if (is_null($test_user)) {
					$errors[] = $this->_T("No such user.");
				}
			}
			if (count($errors) == 0) {
				// send email
				$this->view->showForm = false;
				$this->view->success = $this->_T("Password reset email sent. Please check your email.");
				// prepare notification email
				$subject = $this->_T("Password Reset Link");
				$from = trim(Cts_Registry::get('site_from'));
				$from = $this->_T($from);
				$from_email = trim(Cts_Registry::get('site_from_email'));
				$from_email = $this->_T($from_email);
				$email_params = array(
					"url" => $this->_getConfirmationUrl($test_user->email),
					"from"	=> $from,
					"from_email" => $from_email,
					"locale_code" => $this->locale_code
				);
				$email = new Cts_Email();
				$email->sendEmail($subject, $test_user->email, "password.tpl", $email_params);
			} else {
				$this->view->errors = $errors;
				$this->view->username = $username;
				$this->view->email = $email;
				$this->view->showForm = true;
			}
		} else {
			$this->view->showForm = true;
		
		}
	}

	/*
		Function: postregister
			This action is called after a user has filled out the registration form and it has been processed.
			It's useful for changing the redirect action or tweaking session values.
			This method is also protected from non-logged-in users, so if auto-login is turned on,
			the user will be logged in during this action's init.

		Plugin Hooks:
			- *default_index_postregister_redirect* (filter) - Allows you to change the URI to which the browser is redirected after a user registers. The default is '/user/edit'.
				param redirect_url - The desired redirect URL.
				param session - The session object for the currently authenticated user session.
	*/
	function postregisterAction() {
		$request = new Cts_Request($this->getRequest());
		$redirect_url = Cts_Registry::get('default_postregister_redirect');
		$params = array('redirect_url' => $redirect_url, 'session' => $this->session);
		$params = $this->_cts_plugin->doFilter($this->_mca.'_redirect', $params); // FILTER HOOK
		if ($request->has('url')) {
			// request param trumps all
			$this->_redirect(base64_decode($request->url));
		} else {
			$this->_redirect($params['redirect_url']);
		}
	}

	/*
		Function: profile
			Displays the public profile of a user.
			Uses the _user local variable set up in <init>.
			If the username is invalid, the browser is redirected to '/default/auth/missing' (currently hardcoded).

		Plugin Hooks:
			- *default_user_profile* (filter) - Allows you to affect the user object before displaying it. This is usually used for adding attributes to a user.
				param user - The user object for the specified user
				param request - The current HTTP Request (as a Zend_Request).

		View Variables:
			$params - All params in the param arrays for filters get turned into view variables automatically.

		See Also:
			- <init>
	*/
	function profileAction() {
		if (!is_null($this->_user)) {
			$tmp_user = $this->_user->toArray();
			$usersindex_table = new UsersIndex();
			$usersindex = $usersindex_table->fetchByUsername($this->_username);
			$tmp_user = array_merge($tmp_user, $usersindex->toArray());
			if (!is_null($tmp_user['country_code'])) {
				$countries_table = new Countries();
				$country = $countries_table
					->fetchRow($countries_table->getAdapter()->quoteInto('country_code = ?', $tmp_user['country_code']));
				$tmp_user['location'] = $country->country;
			}
 			$params = array('user' => $tmp_user, 'request' => $this->getRequest());
			$params = $this->_cts_plugin->doFilter($this->_mca, $params); // FILTER HOOK
			foreach ($params as $key => $value) {
				$this->view->$key = $value;
			}
 		} else {
 			$this->_forward('default', 'auth', 'missing'); return;
 		}
	}

	/*
		Function: register
			Either displays a registration form, or processes the registration form,
			depending on whether or not the Request is a GET or POST.
			If there is an active authenticated session, the browser is redirected to the authenticated user's profile (currently hardcoded).
			After a successful registration, the browser is redirected to '/user/postregister' (currently hardcoded).

		HTTP POST Parameters:
			username - The desired username of the registering user.
			email - The email address of the registering user.
			password - The desired password of the registering user.
			confirm - The confirmation of the desired password of the registering user.
			Birthday_Day - The day of the registering user's birth date.
			Birthday_Month - The month of the registering user's birth date.
			Birthday_Year - The year of the registering user's birth date.

		Plugin Hooks:
			- *default_user_register* (filter) - Allows you to affect the user object before it is committed to the database.
				param request - The current HTTP Request (as a Zend_Request).
				param user - The user object for the registering user.
				param errors - An array of errors that will be displayed if not empty.
			- *default_user_register_post_register* (action) - Allows you to perform custom
				processing after a successful registration has occurred and before the browser is redirected.
				param username - The username of the just-registered user.

		View Variables:
			$errors - An array of errors. Only sent if errors are present.
			$pagetitle - The HTML page title.
			$user - A user object for a successfully registered user.
	*/
	function registerAction() {
		$request = new Cts_Request($this->getRequest());

		if ($this->_auth->hasIdentity()) {
			$this->_redirect('/default/user/profile/username/' . $this->_identity->username);
		}
		$users_table = new Users();
		$user = array();

		$pre_register_params = array();

		if ($request->has('url')) {
			$this->view->url_param = $request->url;
			$pre_register_params['return_url'] = $request->url;
		} else {
			$pre_register_params['return_url'] = false;
		}
		
		$pre_register_params = $this->_cts_plugin->doFilter('default_pre_register', $pre_register_params); // FILTER HOOK
		foreach ($pre_register_params as $key=>$value) {
			if( $key=='return_url' ){
				$this->view->url_param = $value;
			} else {
				$this->view->$key = $value;
			}
		}

		if ($this->getRequest()->isPost()) {
			$errors = array();
			$user['username'] = $request->username;
			if($request->has('full_name')){
				if( strlen($request->full_name) < 1){
					$user['full_name'] = $this->_T("Unidentified User");
				} else {
					$user['full_name'] = $request->full_name;
				}
			} else {
				$user['full_name'] = $this->_T("Unidentified User");
			}
			$user['email'] = $request->email;
			$user['password'] = $request->password;
			$user['confirm'] = $request->confirm;
			if ($request->has('Birthday_Day') && $request->has('Birthday_Month') && $request->has('Birthday_Year')) {
				$user['birthday'] = strtotime($request->Birthday_Day ." ". $request->Birthday_Month ." ". $request->Birthday_Year);
			} else {
				$user['birthday'] = null;
			}

			// validate username
			$username_validator = new Zend_Validate();
			$username_validator->addValidator(new Zend_Validate_StringLength(1, Cts_Registry::get('username_length')));
			$username_validator->addValidator(new Zend_Validate_Alnum());

			if (!$username_validator->isValid($user['username'])) {
				$show_username = "'".$user['username']."'";
				if(trim($user['username']) == ""){
					$show_username = "[".$this->_T("empty")."]";
				}
				$errors[] = $this->_T("%s isn't a valid username. (Between %d and %d characters, only letters and numbers)",array($show_username,1,Cts_Registry::get('username_length')));
			}

			$user_where = $users_table->getAdapter()->quoteInto('username = ?', $user['username']);
			if($users_table->getCountByWhereClause($user_where) > 0){
				$errors[] = $this->_T("The username '%s' is already in use",$user['username']);
			}

			// validate email
			$email_validator = new Zend_Validate_EmailAddress();
			if (!$email_validator->isValid($user['email'])) {
				$show_email = "'".$user['email']."'";
				if(trim($user['email']) == ""){
					$show_email = "[".$this->_T("empty")."]";
				}
				$errors[] = $show_email.' '.$this->_T('is not a valid email.');
			}

			// make sure no one is using this email already
			$email_where = $users_table->getAdapter()->quoteInto('email = ?',$user['email']);
			if($users_table->getCountByWhereClause($email_where) > 0){
				$errors[] = $this->_T("Email is already in use.");
			}

			$password_validator = new Zend_Validate();
			$password_validator->addValidator(new Zend_Validate_StringLength(6, 32));
			// make sure password is at least six chars
			if (!$password_validator->isValid($user['password'])){
				$errors[] = $this->_T("Password must be between %d and %d characters",array(6, Cts_Registry::get('password_length')));
			}
			// if password is set, make sure it matches confirm
			if($user['password'] != $user['confirm']){
				$errors[] = $this->_T("Passwords don't match");
			}

			// do we meet the minimum age?
			$minimum_age = Cts_Registry::get('minimum_registration_age', '13') ;
			$years_ago = strtotime($minimum_age . ' years ago');
			if($user['birthday'] > $years_ago){
				$errors[] = $this->_T("You must be at least %d years old to register.", $minimum_age);
			}

			$params = array(
				'request' => $this->getRequest(),
				'user' => $user,
				'errors' => $errors,
			);

			$additional = $this->_cts_plugin->doFilter($this->_mca, $params); // FILTER HOOK

			$errors = $additional['errors'];
			$user = $additional['user'];

			// convert birthday_ts to mysql date
			$birthday_db = date(DB_DATETIME_FORMAT, $user['birthday']);
			if(count($errors) == 0){

				$roles_table = new Roles();
				$users_roles_table = new UsersRoles();
				$default_role_shortname = Cts_Registry::get('default_role_shortname');
				$role_data = array("username" => $user['username'], "role_id" => $roles_table->getIdByShortname($default_role_shortname));
				$users_roles_table->insert($role_data);
				
				$user_data = array(
					'username' => $user['username'],
					'email' => $user['email'],
					'full_name' => $user['full_name'],					
					'birthday' => $birthday_db,
					'password' => $user['password'],
					'created_on' => date("Y-m-d H:i:s"),
					'ip' => getenv('REMOTE_ADDR'),
				);

				if (array_key_exists('about_me', $additional['user'])) {
					$user_data['about_me'] = $additional['user']['about_me'];
				}

				// MAKE IT OFFICIAL
				$users_table->insert($user_data);

				// DO SOME PLUGINS
				$params = array(
					'user' => $user_data,
					'request' => $request,
					'username' => $user['username'],
					'autologin' => true,
					'autologin_username' => $user['username'],
					'autologin_password' => $user['password'],
					'autologin_password_hash' => md5($user['password']),
					'locale_code' => $this->locale_code,
				);
				$params = $this->_cts_plugin->doFilter("default_post_register", $params); // FILTER HOOK
				$this->_cts_plugin->doAction($this->_mca . "_post_register", $params); // ACTION HOOK (deprecated)

				// SET UP AUTO-LOGIN, OR DON'T
				if ($params['autologin']) {
					$appNamespace = new Zend_Session_Namespace('Cts_Temp');
					$appNamespace->autoLogin = $params['autologin'];
					$appNamespace->autoLoginUsername = $params['autologin_username'];
					$appNamespace->autoLoginPassword = $params['autologin_password'];
					$appNamespace->autoLoginPasswordHash = $params['autologin_password_hash'];
				}

				// SEND THE USER ON THEIR WAY
				$url = '/default/user/postregister';
				// if there was a URL passed in then add that encoded URL as a param to the default redirect
				if ($request->has('url')) {
					$url .= '/url/'.$request->url;
				}
				$this->_redirect($url);

			} else {
				$this->view->errors = $errors;
			}

		}

		$this->view->user = $user;
		$this->view->pagetitle = $this->_T("Register");
	}

	/*
		Function: edit
			Either displays an edit form to edit a given user, or processes the edit form,
			depending on whether or not the Request is a GET or POST.
			If the username passed in does not match the currently authenticated user,
			the browser is redirected to '/default/auth/missing' (currently hardcoded).

		Plugin Hooks:
			- *default_user_edit_pre_render* (filter) - For a GET Request, this enables you to affect the user object before the initial form is displayed.
				param user - The user object for the username in the GET Request parameters.
				param request - The entire Request object.
				param session - The entire Session object.
			- *default_user_edit_pre_save* (filter) - Enables you to affect the user object before it is committed to the database.
				param request - The current HTTP Request (as a Zend_Request).
				param user - The user object for the user being edited.
				param errors - An array of errors that will be displayed if not empty.
			- *default_user_edit_post_save* (action) - Enables you to perform an action after the user has been committed to the database.
				param username - The username of the user being edited.

		View Variables:
			$countries - An array of countries pulled from the database.
			$end_year - TBD
			$genders - An array of genders.
			$params - All params in the param arrays for filters get turned into view variables automatically.
	*/
	function editAction() {

		if ($this->_user->username != $this->_identity->username) {
			$this->_forward('default', 'auth', 'missing'); return;
		} else {
			$countries_table = new Countries();
			$this->view->countries = $countries_table->getCountriesArray('Choose a country...');

			$user = $this->_user->toArray();
			$params = array('user' => $user, 'request' => $this->_request, 'session' => $this->session);
			$pre_render = $this->_cts_plugin->doFilter($this->_mca . "_pre_render", $params); // FILTER HOOK
			$user = $pre_render['user'];

			foreach ($pre_render as $key => $value) {
				if ($key != "user") {
					$this->view->$key = $value;
				}
			}
			
			//$tags = unserialize($user->tags);
			if ($this->getRequest()->isPost()) {
				$errors = array();
				$request = new Cts_Request($this->getRequest());
				$request->stripTags(array('email', 'newpassword', 'confirm', 'aboutme'));
				$user['username'] = $this->_identity->username;
				$user['email'] = $request->email;
				$user['full_name'] = $request->full_name;
				$user['password'] = $request->newpassword;
				$user['confirm'] = $request->confirm;
				$user['birthday'] = $birthday = strtotime($request->Birthday_Day . $request->Birthday_Month . $request->Birthday_Year);
				//$user['tags'] = $tag_array = Cts_Common::makeTagArray($request->tags);
				$user['gender'] = $request->gender;
				$user['country_code'] = $request->country_code;
				$user['aboutme'] = $request->aboutme;

				// validate email
				if (!Cts_Validate::checkEmail($user['email'])) {
					$errors[] = $this->_T("Email is not valid");
	   		 	}

				// check to see if email is in use already by someone else
				if ($this->_users_table->isEmailInUse($user['email'],$user['username'])) {
					$errors[] = $this->_T("Email already in use");
				}

				// if password isn't blank, validate it
				if ($user['password'] != ""){
					if(!Cts_Validate::checkLength($user['password'], 6, Cts_Registry::get('password_length'))) {
						$errors[] = $this->_T("Password must be between %d and %d characters",array(6, Cts_Registry::get('password_length')));
	   		 		}
					// if password is set, make sure it matches confirm
					if($user['password'] != $user['confirm']){
						$errors[] = $this->_T("Passwords don't match");
					}
				}

				if (!Cts_Validate::checkLength($user['aboutme'], 0, Cts_Registry::get('user_about_me_length'))) {
					$errors[] = $this->_T("About me must be less than %d characters.",Cts_Registry::get('user_about_me_length'));
				}

				// convert birthday_ts to mysql date
				$birthday = date("Y-m-d H:i:s", $user['birthday']);

				$params = array(
					'request' => $this->getRequest(),
					'user' => $user,
					'errors' => $errors,
				);

				// upload new avatar image if present
				if (array_key_exists('filedata', $_FILES)) {
					if ($_FILES['filedata']['tmp_name'] != '') {
						$users_table = new Users();
						$destination_path = $users_table->getAvatarPath($user['username']);
						$destination_filename = $users_table->getAvatarPath($user['username'], true);
						if (!is_dir($destination_path)) {
						  	mkdir($destination_path, 0777, true);
							Cts_Log::report("Creating user folder at ".$destination_path, null, Zend_Log::DEBUG);
						}
						if (file_exists($destination_filename)) {
							unlink($destination_filename);
							Cts_Log::report("Deleted existing user avatar from ".$destination_path, null, Zend_Log::DEBUG);
						} else {
							Cts_Log::report("User avatar did not exist in ".$destination_path, null, Zend_Log::DEBUG);
						}
						move_uploaded_file($_FILES['filedata']['tmp_name'], $destination_filename);
						Users::clearUserCache($user['username']);
						Cts_Log::report("User avatar uploaded to ".$destination_path, null, Zend_Log::DEBUG);
						$params['user']['hasnewfile'] = true;
					} else {
						$params['user']['hasnewfile'] = false;
					}
				}

				$additional = $this->_cts_plugin->doFilter($this->_mca."_pre_save", $params); // FILTER HOOK
				$errors = $additional['errors'];
				$user = $additional['user'];
				if (strlen($user['full_name']) < 1) {
					$user['full_name'] = $this->_T("Unidentified User");
				}
				if (count($errors) == 0) {
					$data = array(
						'email' => $user['email'],
						'full_name' => $user['full_name'],
						'birthday' => $birthday,
						'aboutme' => nl2br($user['aboutme']),
						'gender' => $user['gender'],
						'country_code' => $user['country_code'],
						//'tags' => serialize($tag_array),
						'last_modified_on' => date(DB_DATETIME_FORMAT),
					);
					if ($user['password'] != "") {
						$data['password'] = $user['password'];
					}

					$where = $this->_users_table->getAdapter()->quoteInto('username = ?', $this->_username);
					$this->_users_table->update($data, $where);
					$this->_cts_plugin->doAction('default_user_edit_post_save', array('username' => $this->_username)); // ACTION HOOK
					$this->view->success = $this->_T("Profile Updated.");

				} else {
					$this->view->errors = $errors;
				}

			}
			//$this->view->tags = Cts_Common::makeTagString($tags);

			$this->view->end_year = -(Cts_Registry::get('minimum_registration_age'));
			// multiply min age by number of seconds in a year
			$this->view->genders = Cts_Common::getGenderArray();
			$user['aboutme'] = Cts_Common::br2nl(stripslashes($user['aboutme']));
			$this->view->user = $user;
		}
	}

	/*
		Function: deleteavatar
			Allows the currently authenticated user to delete their own avatar.
			If the username passed as a parameter doesn't match the currently authenticated user,
			the browser is redirected to '/default/auth/missing' (currently hardcoded).
			After deleting the avatar image file, the user's image cache is cleared so the image does not persist.
			Whether successful or not, the browser is redirected to the referring page.
	*/
	function deleteavatarAction() {
		if ($this->_user->username != $this->_identity->username) {
			$this->_forward('default', 'auth', 'missing'); return;
		} else {
			$avatar_path = $this->_users_table->getAvatarPath($this->_identity->username, true);
			if(file_exists($avatar_path)){
				unlink($avatar_path);
				Users::clearUserCache($this->_identity->username);				
			}
			$this->_redirect('/default/user/edit');
		}
	}

	/* Group: Private or Protected Methods */

	/*
		Function: _getConfirmationUrl
			Used in <forgotpassword> to generate a random secret code for the forgot password email.
			MD5 encryption is used.

		Arguments:
			$email - The email address for which to create a secret validation code.
			$url - The URL to link to from the email.

		Registry Values:
			salt - A secret number used to aid in encryption and decryption. Default is a randomly generated number.
			site_url - The URL of the website. Default is 'http://localhost'.

		Returns:
			A string containing just the newly generated secret code.
	*/
	protected function _getConfirmationUrl($email, $url = "/default/user/resetpassword/email/") {
		$salt = Cts_Registry::get('salt');
		$url_filter = new Cts_Url_Filter();
		$outstr = Cts_Registry::get('site_url').$url_filter->filter($url, array('locale_code' => $this->locale_code)).urlencode($email);
		$outstr .= "/code/".md5($email.$salt);
		return $outstr;
	}

	/*
		Function: _checkConfirmationUrl
			Used in <resetpassword> to verify whether the email address and secret code match.
			MD5 decryption is used.

		Arguments:
			$email - The email address to validate.
			$code - The secret code to validate.

		Registry Values:
			salt - A secret number used to aid in encryption and decryption.

		Returns:
			A boolean (true or false).
	*/
	protected function _checkConfirmationUrl($email, $code) {
		$salt = Cts_Registry::get('salt');
		$test = $email.$salt;
		if (md5($test) == $code) {
			return true;
		} else {
			return false;
		}
	}

}
