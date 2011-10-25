<?php

/*
	Class: RivetyCore_Uri

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_Url_Filter {

	/*
		Function: filter
	*/
	function filter($url, array $params = null) {
		$this->_rivety_plugin = RivetyCore_Plugin::getInstance();
		$out_url = $url;

		// LOCALIZATION
		if (RivetyCore_Registry::get('enable_localization') == '1') {
			if (stripos($url, "/") === 0) {
				// for virtual URLs
				$locale_code = $params['locale_code'];
				if (!is_null($locale_code)) {
					// localize the URL by injecting the locale code at the root
					if (RivetyCore_Translate::validateLocaleCode($locale_code)) {
						$out_url = "/".$locale_code.$url;
					}
				}
			} else {
				// TODO - add other cases, such as absolute and relative URLs
			}
		}

		// SSL
		if (RivetyCore_Registry::get('enable_ssl') == '1') {
			$secure_urls = RivetyCore_Registry::get('secure_urls');
			if (!empty($secure_urls)) {
				$secure_urls = explode('|', $secure_urls);
				if (stripos($url, "/") === 0) {
					// for virtual URLs
					// $tmp_url = '/'.trim('/', $url); // get rid of the last slash
					if (in_array($url, $secure_urls)) {
						$out_url = str_replace('http://', 'https://', RivetyCore_Registry::get('site_url').$out_url);
					} 
				} else {
					// TODO - add other cases, such as absolute and relative URLs
				}
			}
		}

		// FORCE ABSOLUTE URL
		if (array_key_exists('absolute', $params) || in_array('absolute', $params)) {
			if (stripos($url, "/") === 0) {
				$out_url = RivetyCore_Registry::get('site_url').$out_url;
			} else {
				$out_url = RivetyCore_Registry::get('site_url').'/'.$out_url;
			}
		}
		$params = array('url' => $out_url);
		$params = $this->_rivety_plugin->doFilter('url_post_filter', $params); // FILTER HOOK
		return $params['url'];
	}

	public function stripssl($out_url, array $params = null) {
		if (stripos($out_url, "/") === 0) {
			if ($params) {
				$out_url = $this->filter($out_url,$params);
			}
			$out_url = str_replace('https://', 'http://', RivetyCore_Registry::get('site_url').$out_url);
		} else {
			// TODO - add other cases, such as absolute and relative URLs
		}
		return $out_url;
	}


}
