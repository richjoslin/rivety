<?php

/*
	Class: Auth

	About: Author
		Jaybill McCarthy

	About: Contributors
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>

*/
class AuthController extends RivetyCore_Controller_Action_Abstract
{

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
	function loginAction()
	{
		$errors = array();
		$request = new RivetyCore_Request($this->getRequest());
		$users_table = new Users();
		
		if ($request->has('ourl'))
		{
			$url_param = strip_tags($request->ourl);
			$this->view->ourl = $url_param;
			// $this->view->url_param = $url_param;
			$url_param = base64_decode($url_param);
			// $this->view->url_param_decoded = $url_param;
		}

		$params = array('request' => $request);
		$params = $this->_rivety_plugin->doFilter($this->_mca . '_before', $params); // FILTER HOOK
		foreach ($params as $key => $value)
		{
			if ($key != 'request') $this->view->$key = $value;
		}
		unset($params);

		$appNamespace = new Zend_Session_Namespace('RivetyCore_Temp');

		if ($this->getRequest()->isPost() or $appNamespace->autoLogin)
		{
			// collect the data from the user
			$filter = new Zend_Filter_StripTags();
			$appNamespace = new Zend_Session_Namespace('RivetyCore_Temp');
			if ($appNamespace->autoLogin)
			{
				$autologin = true;
				$username = $appNamespace->autoLoginUsername;
				$plain_password = $appNamespace->autoLoginPassword;
				$password = $appNamespace->autoLoginPasswordHash;
				$appNamespace->autoLogin = null;
				$appNamespace->autoLoginUsername = null;
				$appNamespace->autoLoginPassword = null;
				$appNamespace->autoLoginPasswordHash = null;
			}
			else
			{
				$username = $filter->filter($this->_request->getPost('username'));
				$plain_password = $filter->filter($this->_request->getPost('password'));
				$password = $users_table->getPasswordHash($plain_password);
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
			try
			{
				$result = $auth->authenticate($authAdapter);
				if ($result->isValid())
				{
					$appNamespace->last_login = $username;
					// success : store database row to auth's storage system
					// (not the password though!)
					$data = $authAdapter->getResultRowObject(null, 'password');
					$auth->getStorage()->write($data);
					Zend_Loader::loadClass('Zend_Session');
					$defaultNamespace = new Zend_Session_Namespace('Zend_Auth');
					$defaultNamespace->setExpirationSeconds((int)RivetyCore_Registry::get('session_timeout'));

					//update user last_login_on
					
					$where = $users_table->getAdapter()->quoteInto('username = ?', $username);
					$users_table->update(array('last_login_on' => date(DB_DATETIME_FORMAT)), $where);
					$params = array(
						'username' => $username,
						'password' => $plain_password,
						'locale_code' => $this->locale_code,
					);

					if (!empty($url_param)) $params['requested_url'] = $url_param;
					else $params['requested_url'] = null;

					$this->_rivety_plugin->doAction($this->_mca . '_success', $params); // ACTION HOOK
					$this->_rivety_plugin->doAction($this->_mca . '_login_success', $params); // ACTION HOOK (deprecated)

					if ($this->_request->isXmlHttpRequest())
					{
						$user = $users_table->fetchByUsername($username)->toArray();
						$this->view->json = Zend_Json::encode($user);
						$this->_forward('loginajax', $request->controller, $request->module);
						return;
					}

					// TODO - fix view states
					// $redirect_url = RivetyCore_Common::getViewState($this->session, 'last_visited', "/profile/" . $username);

					if ($this->format != 'json')
					{
						$redirect_url = '/default/auth/loginredirect/';
						if (!empty($params['requested_url'])) $redirect_url = $params['requested_url'];
						$this->_redirect($redirect_url);
					}
				}
				else
				{
					// failure: clear database row from session
					$appNamespace->last_login = null;
					$errors[] = $this->_T('Login failed.');
					$params = array('username' => $username);
					$this->_rivety_plugin->doAction($this->_mca . '_failure', $params); // ACTION HOOK
					$this->_rivety_plugin->doAction($this->_mca . '_login_failure', $params); // ACTION HOOK (deprecated)
				}
			}
			catch (Exception $e)
			{
				$appNamespace->last_login = null;
				RivetyCore_Log::report("Login failure.", "Username: [". $username ."] ip: [" . $_SERVER['REMOTE_ADDR'] . "] - " . $e->getMessage() , Zend_Log::WARN);
				$errors = array($this->_T("Login failed."));
			}
		}

		if ($this->_request->isXmlHttpRequest() && !empty($errors))
		{
			$json = array('errors' => $errors);
			$this->view->json = Zend_Json::encode($json);
			$this->_forward('loginajax', $request->controller, $request->module);
			return;
		}

		$this->view->last_login = $appNamespace->last_login;

		foreach ($errors as $error)
		{
			$this->screenAlert('error', $error);
		}
		$errors = null;

		switch ($this->format)
		{
			case 'json': die(!empty($this->screen_alerts) ? json_encode(array('messages' => $this->screen_alerts)) : '200 OK');
			default: break;
		}
	}

	/*
		Function: loginajax
	*/
	function loginajaxAction()
	{
		return;
	}

	/*
		Function: loginredirect
	*/
	function loginredirectAction()
	{
		if ($this->_identity->isAdmin) $this->_redirect(RivetyCore_Registry::get('login_redirect_admins'));
		else                           $this->_redirect(RivetyCore_Registry::get('login_redirect_non_admins'));
	}

	/*
		Function: denied
			This page is shown if the user is not allowed access to the requested page.
			It's typically reached via HTTP Response Redirect.

		Plugin Hooks:
			- *auth_denied* (action) - Allows you to perform actions just before the page renders.
				param username - The username of the logged-in user. Only exists if there is a logged-in user.
	*/
	function deniedAction()
	{
		$params = array();
		if ($this->_auth->hasIdentity()) $params['username'] = $this->_identity->username;
		$this->_rivety_plugin->doAction($this->_mca, $params); // ACTION HOOK
	}

	/*
		Function: unauthorized
	*/
	function unauthorizedAction()
	{
		header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
		$params = array();
		if ($this->_auth->hasIdentity()) $params['username'] = $this->_identity->username;
		$this->_rivety_plugin->doAction($this->_mca, $params); // ACTION HOOK
		die('401 Unauthorized');
	}

	/*
		Function: missing
			This page is shown if the requested page does not exist.
			It's typically reached via HTTP Response Redirect.

		Plugin Hooks:
			- *auth_missing* (action) - Allows you to perform actions just before the page renders.
				param username - The username of the logged-in user. Only exists if there is a logged-in user.
	*/
	function missingAction()
	{
		header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
		$params = array();
		$params['request'] = new RivetyCore_Request($this->getRequest());
		$params['username'] = null;
		if ($this->_auth->hasIdentity())
		{
			$users_table = new Users();
			$user = $users_table->fetchByUsername($this->_identity->username);
			if (!is_null($user))
			{
				$this->view->user = $user->toArray();
				$params['username'] = $user->username;
			}
		}

		$params = $this->_rivety_plugin->doFilter($this->_mca, $params); // FILTER HOOK
		$this->_rivety_plugin->doAction($this->_mca, $params); // ACTION HOOK

		unset($params['request'], $params['username']);
		foreach ($params as $key => $value)
		{
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
	function logoutAction()
	{
		$params = array();
		$params['username'] = null;
		if ($this->_auth->hasIdentity())
		{
			$params['username'] = $this->_identity->username;
			$this->_rivety_plugin->doAction($this->_mca . '_pre', $params); // ACTION HOOK
			Zend_Auth::getInstance()->clearIdentity();
			$this->_rivety_plugin->doAction($this->_mca . '_post', $params); // ACTION HOOK
		}
		switch ($this->format)
		{
			case 'json': die('200 OK');
			default: $this->_redirect('/'); break;
		}
	}

	/*
		Function: error

		Plugin Hooks:
			- *auth_error* (action) - Allows you to perform an action any time an error occurs.
	*/
	function errorAction()
	{
		$this->_rivety_plugin->doAction($this->_mca, array()); // ACTION HOOK
	}

}
