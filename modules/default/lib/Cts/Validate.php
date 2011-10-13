<?php

/*
	Class: Cts_Validate

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>
*/
class Cts_Validate {

	/* Group: Static Methods */

	/*
		Function: checkLength
			Validates the length of a string. Uses Zend_Validate_StringLength.

		Arguments:
			var - The string to validate.
			min - The minimum number of characters for a valid string.
			max - The maximum number of characters for a valid string.

		Returns:
			A boolean indicating whether the string is valid (true) or not (false).

		See Also:
			- Zend_Validate_StringLength
	*/
 	static function checkLength($var, $min, $max) {
        $validator = new Zend_Validate();
		$validator->addValidator(new Zend_Validate_StringLength($min, $max));
		return $validator->isValid($var);
 	}

	/*
		Function: checkEmail
			Validates an email address string. Uses Zend_Validate_EmailAddress.

		Arguments:
			var - The email address string to validate.

		Returns:
			A boolean indicating whether the email address is valid (true) or not (false).

		See Also:
			- Zend_Validate_EmailAddress
	*/
	static function checkEmail($var) {
		$validator = new Zend_Validate_EmailAddress();
		return $validator->isValid($var);
	}

}
