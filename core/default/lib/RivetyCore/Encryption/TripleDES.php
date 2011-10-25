<?php

/*
	Class: RivetyCore_Encryption_TripleDES

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_Encryption_TripleDES {

	/*
		Function: encryptForDotNet
			Copied from the comments in the PHP manual website.
			http://us2.php.net/manual/en/function.mcrypt-encrypt.php#68368
	*/
	public static function encryptForDotNet($key, $vector, $text) {
		$tripledes = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');

		// Pad the text
		$text_add = strlen($text) % 8;
		for ($i = $text_add; $i < 8; $i++) {
			$text .= chr(8 - $text_add);
		}

		mcrypt_generic_init($tripledes, $key, $vector);
		$encrypt64 = mcrypt_generic($tripledes, $text);
		mcrypt_generic_deinit($tripledes);
		mcrypt_module_close($tripledes);

		// Return the encrypt text in 64 bits code
		return bin2hex($encrypt64);
	}

}
