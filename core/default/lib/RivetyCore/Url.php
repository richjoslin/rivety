<?php

/*
	Class: RivetyCore_Url

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>
*/
class RivetyCore_Url {

	/* Group: Static Methods */

	/*
		Function: get
			A wrapper for getting a remote page using CURL.

		Arguments:
			url - The URL to retrieve.
			postfields (optional) - An array of name-value pairs for each HTTP POST field, if needed.
			username (optional) - A username for authenticating, if needed.
			password (optional) - A password for authenticating, if needed.

		Returns:
			An array containing the http response code and page output.
	*/
	static function get($url, array $postfields = null, $username = null, $password = null) {
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_POST, 1);
		if (!is_null($postfields)) {
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($postfields));			
		}
		if (!is_null($username) and !is_null($password)) {
			curl_setopt($curl_handle, CURLOPT_USERPWD, "$username:$password");
		}
		$buffer = curl_exec($curl_handle);
		$output = array('http_code' => curl_getinfo($curl_handle, CURLINFO_HTTP_CODE), 'output' => $buffer);
		curl_close($curl_handle);
		return $output;
	}
}
