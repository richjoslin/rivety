<?php

/*
	Class: Auth

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>

*/
class AuthController extends RivetyCore_Controller_Action_Abstract {

	/* Group: Actions */

	/*
		Function: login
			Either displays a login form, processes a login request, or
			automatically logs someone in (if auto_login in the app variables is set to true).


		Plugin Hooks: 
			- *auth_login_login_success* (action) - Deprecated. Same as auth_login_success.
			- *auth_login_success* (action) - Enables you to perform custom actions before the browser is redirected if the login attempt succeeds.
				param username - The username of the user logging in.
				param password - The password of the user logging in.
				param requested_url - The URL to redirect to after login success and after the plugin has executed.
			- *auth_login_login_failure* (action) - Deprecated. Same as auth_login_failure.
			- *auth_login_failure* (action) - Enables you to perform custom actions if the login attempt failed.
				param username - The username of the user logging in.
				
		HTTP GET or POST Parameters:
			url - the URL that will be redirected to on successful login. must be base64 encoded.				

		View Variables:
			errors - An array of error messages. Only present if errors occurred.
			last_login - The username of the last user to log in, or null if there was a failed login.
			requested_url - the requested URL that was sent with the request, still base64 encoded
			url_param_decoded - requested_url decoded from base64

	*/
	function loginAction() {
		$appNamespace = new Zend_Session_Namespace('RivetyCore_Temp');

		$frontcontroller = Zend_Controller_Front::getInstance();
		$request = $frontcontroller->getRequest();

		if ($request->has('url')) {
			$url_param = strip_tags($request->url);
			$this->view->requested_url = $url_param;
			$this->view->url_param = $url_param;
			$url_param = base64_decode($url_param);
			$this->view->url_param_decoded = $url_param;
		}

		$params = array('request' => $this->getRequest());
		$params = $this->_rivety_plugin->doFilter($this->_mca.'_before', $params); // FILTER HOOK
		foreach ($params as $key => $value) {
			if ($key != 'request') {
				$this->view->$key = $value;
			}
		}
		unset($params);

		if ($this->getRequest()->isPost() or $appNamespace->autoLogin) {
			// collect the data from the user
			$filter = new Zend_Filter_StripTags();
			$appNamespace = new Zend_Session_Namespace('RivetyCore_Temp');
		   if ($appNamespace->autoLogin) {
				$autologin = true;
				$username = $appNamespace->autoLoginUsername;
				$plain_password = $appNamespace->autoLoginPassword;
				$password = $appNamespace->autoLoginPasswordHash;
				$appNamespace->autoLogin = null;
				$appNamespace->autoLoginUsername = null;
				$appNamespace->autoLoginPassword = null;
				$appNamespace->autoLoginPasswordHash = null;
			} else {
				$username = $filter->filter($this->_request->getPost('username'));
				$plain_password = $filter->filter($this->_request->getPost('password'));
				$password = md5($plain_password);
			}
			// setup Zend_Auth adapter for a database table

			$dbAdapter = Zend_Db_Table::getDefaultAdapter();
			$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
			$authAdapter->setTableName('default_users');
			$authAdapter->setIdentityColumn('username');
			$authAdapter->setCredentialColumn('password');

			// Set the input credential values to authenticate against
			$authAdapter->setIdentity($username);
			$authAdapter->setCredential($password);

			// do the authentication
			$auth = Zend_Auth::getInstance();
			try {
				$result = $auth->authenticate($authAdapter);

				if ($result->isValid()) {
					$appNamespace->last_login = $username;
					// success : store database row to auth's storage system
					// (not the password though!)
					$data = $authAdapter->getResultRowObject(null, 'password');
					$auth->getStorage()->write($data);
					Zend_Loader::loadClass('Zend_Session');
					$defaultNamespace = new Zend_Session_Namespace('Zend_Auth');
					$defaultNamespace->setExpirationSeconds(86400);

					//update user last_login_on
					$users_table = new Users();
					$where = $users_table->getAdapter()->quoteInto('username = ?', $username);
					$users_table->update(array('last_login_on' => date(DB_DATETIME_FORMAT)), $where);
					$params = array(
						'username' => $username,
						'password' => $plain_password,
						'locale_code' => $this->locale_code,
					);

					if (!empty($url_param)) {
						$params['requested_url'] = $url_param;
					} else {
						$params['requested_url'] = null;
					}

					$this->_rivety_plugin->doAction($this->_mca.'_success', $params); // ACTION HOOK
					$this->_rivety_plugin->doAction($this->_mca.'_login_success', $params); // ACTION HOOK (deprecated)
					
					if ($this->_request->isXmlHttpRequest()) {
						$user = $users_table->fetchByUsername($username)->toArray();
						$this->view->json = Zend_Json::encode($user);
						$this->_forward('loginajax', $request->controller, $request->module);
						return;
					}

					if (!empty($params['requested_url'])) {
						$this->_redirect( $params['requested_url'] );
					} else {
						// get the last viewed page, or default to the logged in user's profile page
						// TODO - fix view states
						// $this->_redirect(RivetyCore_Common::getViewState($this->session, 'last_visited', "/profile/" . $username));
						$this->_redirect("/default/auth/loginredirect");
					}
				} else {
					// failure: clear database row from session
					$appNamespace->last_login = null;
					$this->view->errors = array($this->_T('Login failed.'));
					$params = array('username' => $username);
					$this->_rivety_plugin->doAction($this->_mca.'_failure', $params); // ACTION HOOK
					$this->_rivety_plugin->doAction($this->_mca.'_login_failure', $params); // ACTION HOOK (deprecated)
				}
			} catch (Exception $e) {
				$appNamespace->last_login = null;
				$this->view->errors = array($e->getMessage());
			}
		}
		
		if ($this->_request->isXmlHttpRequest() && !empty($this->view->errors)) {
			$json = array('errors' => $this->view->errors);
			$this->view->json = Zend_Json::encode($json);
			$this->_forward('loginajax', $request->controller, $request->module);
			return;
		}
		
		$this->view->last_login = $appNamespace->last_login;
	}
	
	/*
		Function: loginajax
	*/
	function loginajaxAction() {
		return;
	}

	/*
		Function: loginredirect
	*/
	function loginredirectAction() {
		if ($this->_identity->isAdmin) {
			$this->_redirect(RivetyCore_Registry::get('login_redirect_admins'));
		} else {
			$this->_redirect(RivetyCore_Registry::get('login_redirect_non_admins'));
		}
	}

	/*
		Function: denied
			This page is shown if the user is not allowed access to the requested page.
			It's typically reached via HTTP Response Redirect.

		Plugin Hooks:
			- *auth_denied* (action) - Allows you to perform actions just before the page renders.
				param username - The username of the logged-in user. Only exists if there is a logged-in user.
	*/
	function deniedAction() {
		$params = array();
		if ($this->_auth->hasIdentity()) {
			$params['username'] = $this->_identity->username;
		}
		$this->_rivety_plugin->doAction($this->_mca, $params); // ACTION HOOK
	}

	/*
		Function: missing
			This page is shown if the requested page does not exist.
			It's typically reached via HTTP Response Redirect.

		Plugin Hooks:
			- *auth_missing* (action) - Allows you to perform actions just before the page renders.
				param username - The username of the logged-in user. Only exists if there is a logged-in user.
	*/
	function missingAction() {
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
		$params = array();
		$params['request'] = new RivetyCore_Request($this->getRequest());
		$params['username'] = null;
		if ($this->_auth->hasIdentity()) {
			$users_table = new Users();
			$user = $users_table->fetchByUsername($this->_identity->username);
			if (!is_null($user)) {
				$this->view->user = $user->toArray();
				$params['username'] = $user->username;
			}
		}
		
		$params = $this->_rivety_plugin->doFilter($this->_mca, $params); // FILTER HOOK
		$this->_rivety_plugin->doAction($this->_mca, $params); // ACTION HOOK
		
		unset($params['request'], $params['username']);
		foreach ($params as $key => $value) {
			$this->view->$key = $value;
		}
	}

	/*
		Function: logout
			Logs a user out of their session and redirects to the the app root ('/', currently hardcoded).

		Plugin Hooks:
			- *auth_logout_pre* (action) - Allows you to perform actions before the user is logged out.
				param username - The username of the logged-in user. Only exists if there is a logged-in user.
			- *auth_logout_post* (action) - Allows you to perform actions after the user is logged out and before the page is redirected. You could override the redirect here.
				param username - The username of the logged-in user. Only exists if there is a logged-in user.
	*/
	function logoutAction() {
		$appNamespace = new Zend_Session_Namespace('RivetyCore_Temp');
		$appNamespace->requestedUrl = null;
		$params = array();
		$params['username'] = null;
		if ($this->_auth->hasIdentity()) {
			$params['username'] = $this->_identity->username;
		}
		$this->_rivety_plugin->doAction($this->_mca . '_pre', $params); // ACTION HOOK
		Zend_Auth::getInstance()->clearIdentity();
		$this->_rivety_plugin->doAction($this->_mca . '_post', $params); // ACTION HOOK
		$this->_redirect('/');
	}

	/*
		Function: error

		Plugin Hooks:
			- *auth_error* (action) - Allows you to perform an action any time an error occurs.
	*/
	function errorAction() {
		$this->_rivety_plugin->doAction($this->_mca, array()); // ACTION HOOK
	}

}
