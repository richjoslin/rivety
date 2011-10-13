<?php

/*
	Class: Locale

	About: Author
		Rich Joslin

	About: License
		<http://communit.as/docs/license>

	About: See Also
		<Cts_Controller_Action_Abstract>

*/
class LocaleController extends Cts_Controller_Action_Abstract {

	/* Group: Actions */

	/*
		Function: choose
	*/
	function chooseAction() {
		// TODO maybe? - prevent people from viewing this page if localization is not enabled
		$default_locale = strtolower(Cts_Registry::get('default_locale', 'default'));
		$request = new Cts_Request($this->getRequest());

		// TODO - get cookie value and validate it

		// TODO - only redirect if a valid cookie value exists !
		if (!$request->has('change') && false) {
			$this->_redirect("/".$this->locale_code);
		}
		
		// Force the use of en-US
		if ($this->locale_code != $default_locale) {
			$this->locale_code = $default_locale;
			$this->_redirect("/".$this->_request->getModuleName()."/".$this->_request->getControllerName()."/".$this->_request->getActionName()."/");
		}
		
		$locales_table = new Locales();
		$tmp_regions = $locales_table->getDistinctRegions();
		$choices = array();

		foreach ($tmp_regions as $tmp_region) {
			if ($tmp_region['region_name'] == 'Global') {
				continue;
			}
			
			$tmp_countries = $locales_table->getDistinctCountries($tmp_region['region_name']);
			$tmp_pseudo_countries = $locales_table->getDistinctPseudoCountryCodes($tmp_region['region_name']);
			foreach ($tmp_countries as $tmp_country) {
				if (!empty($tmp_pseudo_countries) && in_array($tmp_country['country_code'], $tmp_pseudo_countries[0])) {
					continue;
				}
				
				$tmp_lang = $locales_table->getLanguages($tmp_region['region_name'], $tmp_country['country_code']);
				$tmp_country['languages'] = array();
				foreach ($tmp_lang as $lan) {
					if (!is_null($lan['pseudo_country_code'])) {
						$tmp_locale_code = strtolower($lan['language_code']."-".$lan['pseudo_country_code']);
					} else {
						$tmp_locale_code = strtolower($lan['language_code']."-".$tmp_country['country_code']);
					}

					if (in_array($tmp_locale_code,$this->live_locales)) {
						$tmp_country['languages'][] = $lan;
					}
				}

				if (count($tmp_country['languages']) > 0) {
					$tmp_region['countries'][] = $tmp_country;
				}
			}

			if (array_key_exists('countries',$tmp_region) && count($tmp_region['countries']) > 0) {
				$choices[] = $tmp_region;
			}
		}
		
		$this->view->choices = $choices;
		if ($request->has('inline')) {
			$this->view->inline = $request->inline;
		}
	}

	/*
		Function: setcookie
	*/
	function setcookieAction() {
		// TODO maybe? - prevent people from viewing this page if localization is not enabled
		$request = new Cts_Request($this->getRequest());
		if ($request->has("code") && $request->code != "") {
			$locale_code = $request->code;
			$time = Cts_Registry::get('locale_cache_lifetime');
			if (Cts_Translate::validateLocaleCode($locale_code)) {
				setcookie("locale_code", $locale_code, time() + $time , "/");
				if ($request->has("return_url")) {
					$url_filter = new Cts_Url_Filter();
					header("Location: ".$url_filter->filter($request->return_url, array('locale_code' => $locale_code)));
				} else {
					header("Location: /".$locale_code);
				}
			}
		} else {
			$this->_redirect("/default/locale/choose/");
		}
	}

}
