<?php

/*
	Class: Cts_ReCaptcha

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>
*/
class Cts_ReCaptcha {

	/* Group: Instance Methods */

	/*
		Function: verify

		Arguments:
			remote_ip - The IP of the user.
			private_key - The application's private key.
			challenge - TBD
			response - TBD

		Returns:
			boolean
	*/
	
	function curl($public_key) {
		$ch = curl_init("http://api.recaptcha.net/noscript?k=".$public_key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		
		$output = curl_exec($ch);
		curl_close($ch);
		
		if ($output) {
			$image = substr($output, strpos($output, '<img'));
			$image = substr($image, strpos($image, 'src="')+5);
			$image = 'http://api.recaptcha.net'.substr($image, 0, strpos($image, '"'));
			
			$challenge = substr($output, strpos($output, 'id="recaptcha_challenge_field"')+30);
			$challenge = substr($challenge, strpos($challenge, 'value="')+7);
			$challenge = substr($challenge, 0, strpos($challenge, '"'));
			
			return array(
				'image'=>htmlspecialchars($image),
				'challenge'=>htmlspecialchars($challenge)
			);
		} else {
			return false;
		}
	}
	
	function verify($remote_ip, $private_key, $challenge, $response) {

		$url = "http://api-verify.recaptcha.net/verify";
		$data = array(
			'remoteip'   => $remote_ip,
			'privatekey' => $private_key,
			'challenge'  => $challenge,
			'response'   => $response,
		);

		$response = Cts_Url::get($url, $data);

		if ($response['http_code'] == 200) {
			$rc_response = explode(chr(10), $response['output'], 2);
			if ($rc_response[0] == "true") {
				$verify = true;
			} else {
				$verify = false;
			}
		} else {
			$verify = false;
		}
		return $verify;
	}
}
