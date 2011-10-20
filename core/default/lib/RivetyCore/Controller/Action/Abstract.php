<?php

/*
	Class: RivetyCore_Controller_Action_Abstract

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		Zend_Controller_Action
*/
abstract class RivetyCore_Controller_Action_Abstract extends Zend_Controller_Action {

	/* Group: Instance Variables */

	/*
		Variable: $_theme_locations
	*/
	protected $_theme_locations;

	/*
		Variable:
	*/
	protected $_debug_mode;

	/* Group: Instance Methods */

	/*
		Function: init
			Initializes the current instance.

		View Variables:
			host_id - if specified in the config.ini, this will be set to the host name. Used primarily in sites with multiple mirrored machines.
			site_url - The base URL of the website. Pulled from the registry.
			site_name - The value of site_name from the registry.
			admin_role - The name of the admin role. Pulled from the registry.
			mca - A concatenation of the names of the model, controller, and action.
			Useful as a body class selector when you want different models,
			controllers, and/or actions to be styled differently. Does not account for routes. Use gingerly.
			controller_name - The name of the current controller.
			module_name - The name of the current module.
			action_name - The name of the current action.
			isLoggedIn - Indicates whether the current user is logged in. (boolean)
			loggedInUsername - The username of the current user if they are logged in, or null otherwise.
			loggedInFullName - The full name of the current user if they are logged in, or null otherwise.
			loggedInRoleId - The role ID of the current user if they are logged in, or null otherwise.
			loggedInUser - Array containing the data from the row in the user table of the logged in user. Does not exist if user is not logged in.
			isAdmin - Indicates whether the current user is a member of the administrator role. (boolean)
			last_login - The last login fate of the currently logged in user
			format_date - A format string for date. Pulled from the registry.
			format_datetime - A format string for date with time. Pulled from the registry.
			format_datetime_small - A format string for short date and time. Pulled from the registry.
			current_year - The current year as a 4 digit integer.
			theme_path - full path to the base dir of the current theme
			theme_url - relative url of the base dir current theme
			theme_global_path - full path to the /global dir of the current theme
			theme_controller_path - full path to the current controller's template directory
			theme_module_path - full path to the default module's theme directory
			default_theme_path - full path to the base dir of the default theme
			default_theme_url - relative url of the base dir default theme
			default_theme_global_path - full path to the /global dir of the default theme
			default_theme_controller_path - full path to the current controller's default template directory
			default_theme_module_path - full path to the current module's default theme directory
			title_prefix - Set in the config. Can be used by themes in the title tag
			view_states - An array of key value pairs (stored in the session) representing the state of certain pages.
				For instance, the last time you viewed the list of users, you were on page 3.
						isAdminController - a boolean that specifies if this is an admin controller.
			module_xxxx - If module xxxx is enabled, this will be true. If it's not, this won't exist.

		Plugin Hooks:
			- *controller_init* (filter) - Allows you to perform additional actions just before the init method terminates.
				param username - The username of the current user if they are logged in, or null otherwise.
			- *controller_nav* (filter) - Allows you to modify the navigation array before it is assigned to the view.
				param nav_items - The array of links returned by the Navigation model class.
				param request - The HTTP Request object.
	*/
	function init() {
		$params = array('username' => null);
		$modules_table = new Modules('core');
		$roles_table = new Roles();
		$enabled_modules = $modules_table->getEnabledModules();

		foreach ($enabled_modules as $enabled_module) {
			$this->view->{"module_".$enabled_module} = true;
		}

		if (!empty($_SERVER['HTTPS'])) {
			$this->view->is_ssl = true;
			$this->_is_ssl = true;
		} else {
			$this->view->is_ssl = false;
			$this->_is_ssl = false;
		}

		$this->_uri = $_SERVER['REQUEST_URI'];
		$this->_host_id = Zend_Registry::get('host_id');
		$this->view->host_id = $this->_host_id;
		$this->view->session_id = Zend_Session::getId();
		$this->view->site_url = RivetyCore_Registry::get('site_url');
		$this->view->site_name = RivetyCore_Registry::get('site_name');
		$this->registry = Zend_Registry::getInstance();
		$this->session = new Zend_Session_Namespace('Default');

		$this->_debug_mode = ($this->_request->has('debug') && $this->_request->debug == 'dump');

		$this->_mca = $this->_request->getModuleName()."_".$this->_request->getControllerName()."_".$this->_request->getActionName();
		$this->view->mca = str_replace("_", "-", $this->_mca);
		$this->view->controller_name = $this->_request->getControllerName();
		$this->module_name = $this->_request->getModuleName();
		$this->view->module_name = $this->_request->getModuleName();
		$this->view->action_name = $this->_request->getActionName();
		$this->_auth = Zend_Auth::getInstance();
		if ($this->_auth->hasIdentity()) {
			$this->_identity = $this->_auth->getIdentity();
			$this->view->isLoggedIn = true;
			$params['username'] = $this->_identity->username;
			$users_table = new Users();
			$loggedInUser = $users_table->fetchByUsername($this->_identity->username);
			if (!is_null($loggedInUser)) {
				$this->_loggedInUser = $loggedInUser;
				$this->view->loggedInUser = $loggedInUser->toArray();
			}
			$this->view->loggedInUsername = $this->_identity->username;
			$this->view->loggedInFullName = $this->_identity->full_name;

			$loggedInRoleIds = $roles_table->getRoleIdsByUsername($this->_identity->username);
			$this->view->loggedInRoleIds = $loggedInRoleIds;

			foreach ($loggedInRoleIds as $role_id) {
				$role = $roles_table->fetchRow('id = '.$role_id);
				if ((boolean)$role->isadmin) {
					$this->view->isAdmin = true;
					$this->_identity->isAdmin = true;
				}
			}

		} else {
			$this->_identity = null;
			$this->view->isLoggedIn = false;
		}
		$appNamespace = new Zend_Session_Namespace('RivetyCore_Temp');
		$this->view->last_login = $appNamespace->last_login;

		$this->_rivety_plugin = RivetyCore_Plugin::getInstance();

		$this->_theme_locations = Zend_Registry::get('theme_locations');

		// Theme filter block: Allow plugins to alter the current theme based on request, locale, etc.
		$theme_params = array(
			'request' => $this->_request,
			'admin' => array(
				'current_theme' => $this->_theme_locations['admin']['current_theme'],
			),
			'frontend' => array(
				'current_theme' => $this->_theme_locations['frontend']['current_theme'],
			)
		);
		$theme_params = $this->_rivety_plugin->doFilter('current_themes', $theme_params); // FILTER HOOK
		if (file_exists($theme_params['admin']['current_theme']['path']))
		{
			$this->_theme_locations['admin']['current_theme'] = $theme_params['admin']['current_theme'];
		}
		if (file_exists($theme_params['frontend']['current_theme']['path']))
		{
			$this->_theme_locations['frontend']['current_theme'] = $theme_params['frontend']['current_theme'];
			$template_path = $this->_theme_locations['frontend']['current_theme']['path'] . "/tpl_controllers/" . $this->module_name;

			$this->view->setScriptPath($template_path);

		}
		// Theme filter block: End.

		$this->view->theme_path = $this->_theme_locations['frontend']['current_theme']['path'];
		$this->view->theme_url = $this->_theme_locations['frontend']['current_theme']['url'];
		$this->view->theme_global_path = $this->_theme_locations['frontend']['current_theme']['path'] . "/tpl_common";
		$this->view->theme_global = $this->view->theme_global_path;
		$this->view->theme_controller_path = $this->_theme_locations['frontend']['current_theme']['path'] . '/tpl_controllers/' . $this->getRequest()->getControllerName();
		$this->view->theme_module_path = $this->_theme_locations['frontend']['current_theme']['path'] . '/tpl_controllers';

		$this->view->default_theme_path = $this->_theme_locations['frontend']['default_theme']['path'];
		$this->view->default_theme_url = $this->_theme_locations['frontend']['default_theme']['url'];
		$this->view->default_theme_global_path = $this->_theme_locations['frontend']['default_theme']['path'] . "/tpl_common";
		$this->view->default_theme_controller_path = $this->_theme_locations['frontend']['default_theme']['path'] . '/tpl_controllers/' . $this->module_name . "/" . $this->getRequest()->getControllerName();
		$this->view->default_theme_module_path = $this->_theme_locations['frontend']['default_theme']['path'] . '/tpl_controllers/' . $this->module_name;

		RivetyCore_Log::report("Current path ".$this->_mca, null, Zend_Log::INFO);

		$this->view->isAdminController = false;

		$this->view->title_prefix = RivetyCore_Registry::get('title_prefix');

		$locale_is_valid = true;
		$default_locale_code = str_replace('_', '-', trim(strtolower(RivetyCore_Registry::get('default_locale'))));
		$this->locale_code = $default_locale_code;

		$localization_enabled = RivetyCore_Registry::get('enable_localization');
		$this->view->localization_enabled = $localization_enabled;

		if ($localization_enabled == '1')
		{
			// to set the locale code, look in the URL, not in the cookie
			// the only thing that should check the cookie is the home page and optionally the locale chooser page
			$locales_table = new Locales();
			$db_locales_full = $locales_table->getLocaleCodesArray(true);
			$db_locales = array_keys($db_locales_full);

			// Get the locales allowed in the config
			$allowed_locales = explode(',', RivetyCore_Registry::get('allowed_locales'));
			if (!empty($allowed_locales) && (bool)array_filter($allowed_locales)) {
				$allowed_locales = array_map('trim', $allowed_locales);
				$allowed_locales = array_map('strtolower', $allowed_locales);
				$allowed_locales = str_replace('_', '-', $allowed_locales);
			} else {
				throw new Exception('Localization is enabled, but no locales are set in `allowed_locales`');
			}

			// Load the allowed locales into Smarty for the admin drop down
			$all_locales = array();
			foreach ($db_locales_full as $code => $name)
			{
				if (in_array($code, $allowed_locales))
				{
					$all_locales[$code] = $name;
				}
			}
			$this->view->locale_codes = $all_locales;

			// Get the locales allowed on the frontend in the config
			$live_locales = explode(',', RivetyCore_Registry::get('live_locales'));
			if (!empty($live_locales) && (bool)array_filter($live_locales))
			{
				$live_locales = array_map('trim', $live_locales);
				$live_locales = array_map('strtolower', $live_locales);
				$live_locales = str_replace('_', '-', $live_locales);
				$this->live_locales = $live_locales;
			}
			else
			{
				throw new Exception('Localization is enabled, but no locales are set in `live_locales`');
			}

			if ($this->_request->has('locale') && $this->_request->locale != '')
			{
				$locale_code = $this->_request->get('locale');
				if ($locale_code !== $default_locale_code)
				{
					if (ereg("^..-.{2,5}", $locale_code) !== false)
					{
						// Get the locales out of the database
						if ( !in_array($locale_code, $db_locales) || !in_array($locale_code, $allowed_locales))
						{
							$locale_is_valid = false;
						}
						if ($this->view->isAdmin !== true)
						{
							if (!in_array($locale_code, $this->live_locales))
							{
								$locale_is_valid = false;
							}
						}
					}
					else
					{
						$locale_is_valid = false;
					}
				}
				$locale_params = array('request' => $this->_request, 'locale_code' => $locale_code, 'locale_is_valid' => $locale_is_valid);
				$locale_params = $this->_rivety_plugin->doFilter('validate_locale', $locale_params); // FILTER HOOK
				$locale_code = $locale_params['locale_code'];
				$locale_is_valid = $locale_params['locale_is_valid'];
				if ( $locale_is_valid == true)
				{
					// The locale is good.
					$this->locale_code = $locale_code;
					$this->default_locale_code = $default_locale_code;
					$this->view->locale_code = $locale_code;
					$this->view->default_locale_code = $default_locale_code;
					$this->view->request_locale = $locale_code;
					$this->view->default_locale_code = $default_locale_code;
				} else {
					if (strtolower($locale_code) !== $locale_code) {
						// The locale is probably just upper case. Try lower case.
						$this->locale_code = strtolower($locale_code);
						$url = str_replace("/{$locale_code}/", '/', $_SERVER['REDIRECT_URL']); // See Apache Quirks: http://framework.zend.com/manual/en/zend.controller.request.html
						$this->_redirect($url, array('code' => 301));
					} else {
						// This locale is just bad.
						$this->locale_code = $default_locale_code;
						$this->view->locale_code = $default_locale_code;

						// Checking hasIdentity() here would be incorrect, as guests do not have identities, but may have access to this action
						if (@RivetyCore_ResourceCheck::isAllowed("choose", "default", $this->_identity->username, 'Locale')) {
							$this->_redirect("/default/locale/choose/");
						} else {
							if (empty($this->_request->locale)) {
								$this->_redirect("/", array('code' => 301));
							} else {
								$this->_redirect("/default/auth/missing/");
							}
						}
					}
				}
			} elseif ($this->_mca == "default_index_index" && isset($_COOKIE['locale_code'])) {
				$this->_redirect("/".$_COOKIE['locale_code']."/", array(), false);
			} else {
				// Checking hasIdentity() here would be incorrect, as guests do not have identities, but may have access to this action
				if (@RivetyCore_ResourceCheck::isAllowed("choose", "default", $this->_identity->username, 'Locale')) {
					$this->_redirect($default_locale_code."/default/locale/choose/");
				} else {
					$this->_redirect($default_locale_code."/default/auth/missing/");
				}
			}
		}

		$this->view->custom_metadata = RivetyCore_Registry::get('custom_metadata');

		$language = substr($this->locale_code, 0, strpos($this->locale_code, '-'));

		$this->view->format_date = RivetyCore_Registry::get('format_date');
		$this->view->format_datetime = RivetyCore_Registry::get('format_datetime');
		$this->view->format_datetime_small = RivetyCore_Registry::get('format_datetime_small');
		// TODO - figure out an awesome way to switch date formats based on locale
		$this->view->current_year = date("Y");

		// SAVED FOR FUTURE USE - changing the language pack based on locale
		// $locale_table = new Locales();
		// $locale_data = $locale_table->fetchByLocaleCode($this->view->locale_code);
		// if (count($locale_data) > 0) {
		// 	$this->locale_data = $locale_data['0'];
		// 	$this->view->locale_data = $this->locale_data;
		// 	$lan_pk = $this->locale_data['language_code'].'_'.$this->locale_data['country_code'].'.UTF-8';
		// 	setlocale(LC_ALL, $lan_pk);
		// 	setlocale(LC_NUMERIC, 'en_US.UTF-8');
		// 	setlocale(LC_COLLATE, 'en_US.UTF-8');
		// }

		// this is a way to force the browser to reload some scripts
		if (RivetyCore_Registry::get('uncache_css_js_version')) {
			$this->view->uncache_version = "?v=".RivetyCore_Registry::get('uncache_css_js_version');
		}
		if (RivetyCore_Registry::get('uncache_flash_version') ){
			$this->view->uncache_flash = "?v=".RivetyCore_Registry::get('uncache_flash_version');
		}

		// Set the content type to UTF-8
		header('Content-type: text/html; charset=UTF-8');

		// get navigation items from database or cache

		// check for role of identity, if we don't have one, use guest.
		// TODO - move this to the place where role is determined, there should only be one place
		if ($this->_auth->hasIdentity()) {
			$tmp_ids = $loggedInRoleIds;
			$this->my_roles = $roles_table->fetchRolesByUsername($this->_identity->username)->toArray();
			$username = $this->_identity->username;
			$this->view->username = $username;
		} else {
			$tmp_ids = array($roles_table->getIdByShortname("guest"));
			$this->my_roles = array(0 => array(
				"id" => "1",
				"shortname" => "guest",
				"description" => "Guest",
				"is_admin" => "0",
				"isguest" => "1",
				"isdefault" => "0")
			);
		}
		$this->view->my_roles = $this->my_roles;

		// find the parent roles, add the parent role IDs to the nav_role_ids for inheritance.

		$nav_parent_role_ids = array();
		foreach($tmp_ids as $nav_role){
			$nav_parent_role_ids = array_merge($nav_parent_role_ids, $roles_table->getAllAncestors($nav_role));
		}
		$nav_role_ids = array();
		$nav_role_ids = array_merge($nav_parent_role_ids, $tmp_ids);
		$unique_ids = array_unique($nav_role_ids);
		sort($unique_ids);

		$nav_table = new Navigation($unique_ids, $this->locale_code);

		$cache_name = 'navigation_'.$this->locale_code.'-'.md5(implode($unique_ids, "-")); // MD5 The Unique IDs to shorten the cache name
		$cache_tags = array('navigation', $this->locale_code);
		$nav_items_temp = false;
		if (RivetyCore_Registry::get('enable_navigation_cache') == '1') {
			$nav_items_temp = RivetyCore_Cache::load($cache_name);
		}
		if ($nav_items_temp === false || !isset($nav_items_temp)) {
			$nav_items_temp = array();
			foreach ($unique_ids as $nav_role_id) {
				$nav_items_temp = array_merge($nav_items_temp, $nav_table->getNavTree($nav_role_id));
			}
			if (RivetyCore_Registry::get('enable_navigation_cache') == '1') {
				RivetyCore_Cache::save($nav_items_temp, $cache_name, $cache_tags);
			}
		}

		$navparams = array('nav_items' => $nav_items_temp, 'request' => $this->_request, 'locale_code' => $this->locale_code);
		$navparams = $this->_rivety_plugin->doFilter('controller_nav', $navparams); // FILTER HOOK
		$this->view->nav_items = $navparams['nav_items'];
		// TODO - Rich fix this
		// // VIEW STATES
		// if (!$this->session->view_states) {
		// 	$this->session->view_states = array();
		// }
		// // TODO - allow use of regular expressions such as /auth/*
		// $last_visited_pages_filter = explode('|', RivetyCore_Registry::get('last_visited_pages_filter'));
		// if (!in_array($this->_uri, $last_visited_pages_filter)) {
		// 	$this->session->view_states['last_visited'] = $this->_uri;
		// }
		// $this->view->view_states = $this->session->view_states;

		// CONTROLLER INIT HOOK
		$params['request'] = $this->_request;
		$params['locale_code'] = $this->locale_code;
		$params['session'] = $this->session;
		$additional = $this->_rivety_plugin->doFilter('controller_init', $params); // FILTER HOOK
		unset($additional['request']); // we don't want to send the request to the view
		if( isset($additional['filter_redirect']) ){
			$this->_redirect($additional['filter_redirect']);
		}
		foreach ($additional as $key => $value) {
			$this->view->$key = $value;
		}
	}

	/* Group: Private or Protected Methods */

	/*
		Function: _T
	*/
	protected function _T($key, $replace = null, $do_translate = true) {
		return RivetyCore_Translate::translate($this->locale_code, 'default', $key, $replace, $do_translate);
	}

	// TODO - MOVE ALL PAGING METHODS TO A SEPARATE LIBRARY

	/*
		Function: makePager
			Adds paging information to a view. This works independently of any collection.
			You basically describe the collection to this method and you get back some view variables.

		Arguments:
			$page - The index of the page to display. (0-based)
			$per_page - The number of items to display per page.
			$total - The total number of items in the entire collection.
			$url - The URL of the page on which the pager is being displayed.
			$params (optional) - An open-ended customizable array of key-value-paired parameters.
				Pairs passed in will be added to the $url argument as parameters.
				Default is null.

		View Variables:
			pager_url - The $url argument with optional $params added to it.
			page_info - Pager status. Information about the page you are on (e.g.: 11 to 20 of 87)
			next - The index of the page after the current page. (0-based)
			last - The index of the last page in the entire collection. (0-based)
			prev - The index of the page before the current page. (0-based)
			first - The index of the first page in the entire collection. (0-based)
			total - The total number of items being paged. This is the $total argument passed out unaltered.
			page - The index of the current page. (0-based)
			display_page - The index of the current page. (1-based)
			total_pages - The total number of pages in the entire collection.
			pages - An array of integers, one for each page in the entire collection. (1-based)
	*/
	protected function makePager($page, $per_page, $total, $url, $params = null) {
		if (!is_null($params)) {
		  	foreach ($params as $key => $val) {
		  		if (!is_null($val)) {
		  			$url .= "/".$key."/".$val;
		  		}
		  	}
		}
	  	$this->view->pager_url = $url;
	  	if ($total > $per_page) {
	  		$total_pages = ceil($total / $per_page);
	  	} else {
	  		$total_pages = 1;
	  	}
		$start = ($per_page * $page);
		$end = $start + count($total);
		$start++;
		$this->view->page_info = "$start to $end of $total";

		if ($page < $total_pages - 1) {
			$this->view->next = $page + 1;
			$this->view->last = $total_pages - 1;
		}

		if ($page > 0) {
			$this->view->prev = $page - 1;
			$this->view->first = 0;
		}

		$this->view->total = $total;
		$this->view->page = $page;
		$this->view->display_page = $page + 1;
		$this->view->total_pages = $total_pages;
		$pages = array();
		for ($x = 0; $x < $total_pages; $x++ ) {
			$pages[] = $x;
		}
		$this->view->pages = $pages;
	}

	/*
		Function: _redirect
	*/
	protected function _redirect($url, array $options = array(), $auto_add_locale = true) {
		if ($auto_add_locale) {
			$urlfilter_params = array('locale_code' => $this->locale_code);
		}
		$url_filter = new RivetyCore_Url_Filter();
		return parent::_redirect($url_filter->filter($url, $urlfilter_params), $options);
	}

	protected function debug($var_name, $var) {
		if ($this->_debug_mode === true)
		{
			echo("\$" . $var_name . " =");
			d($var);
		}
	}

	protected function actionFinish()
	{
		if ($this->_debug_mode) die("debug mode output complete");
	}

}
