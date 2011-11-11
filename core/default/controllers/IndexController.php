<?php

/*
	Class: Index

	About: Author
	Jaybill McCarthy

	About: License
	<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>

*/
class IndexController extends RivetyCore_Controller_Action_Abstract
{

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			Initializes the current instance.
			Also initializes the parent object (calls init() on the parent instance).
	*/
	function init()
	{
		parent::init();
	}

	/* Group: Actions */

	/*
		Function: index
			This is the web application homepage. It doesn't really do anything by itself. Because everyone
			wants something different on the front page, you can add stuff to this page via a filter hook.
			It will also correctly redirect to the default locale instance if none is set.

		Plugin Hooks:
			- *default_index_index* (filter) - Enables adding view variables via plugin.
				param local_code - if localization is enabled, this will be set to the current locale code
				param is_admin - boolean set to true if the logged in user is an admin (admin flag set true on role)
				param request - the ZF request object sent to this action

		View Variables:
			params - All params in the param arrays for filters get turned into view variables automatically.
	*/
	function indexAction()
	{
		$params = array(
			'locale_code' => $this->locale_code,
			'request' => $this->getRequest(),
		);
		if ($this->_auth->hasIdentity()) {
			$params['is_admin'] = $this->_identity->isAdmin;
		} else {
			$params['is_admin'] = false;
		}
		$additional = $this->_rivety_plugin->doFilter($this->_mca, $params); // FILTER HOOK
		foreach($additional as $key => $value) {
			$this->view->$key = $value;
		}

		// $this->view->welcome = $this->_T("Welcome!");
		// TODO - find out if there is a valid cookie
		// then redirect to that locale
		// or redirect to the default locale
		// ONLY if localization is enabled

		// if localization is enabled and the URI does not contain a locale code
		// and there is not a valid locale cookie
		// redirect to a URI that contains the default locale code
		if (RivetyCore_Registry::get('enable_localization') == '1') {
			$locales_table = new Locales();
			$locale_codes = $locales_table->getLocaleCodes(true);
			$uri_parts = explode("/", trim($this->_uri, "/"));
			if (count($uri_parts) > 0 && !in_array($uri_parts[0], $locale_codes)) {
				// redirect method will automatically add the correct locale code to the URI
				$this->_redirect("/");
			}
		}

// $nav_items
	}

}
