<?php

/*
	Class: RivetyCore_Request

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_Request {

	/*
		Group: Properties
	*/

	protected $_validators = array();

	protected $_validation_errors = array();

	function getValidationErrors() {
		return $this->_validation_errors;
	}

	/* Group: Constructors */

	/*
		Constructor: __construct

		Arguments:
			request - An HTTP Request object.

		Returns:
			void
	*/
	function __construct($request) {
		$params = $request->getParams();
		foreach ($params as $name => $param) {
			if (!is_array($param)) {
				$param =  trim($param);
				if (strlen($param) == 0) {
					$this->$name = null;
				} else {
					$this->$name = $param;
				}
			} else {
				$this->$name = $param;
			} 
		}
	}

	/* Group: Instance Methods */

	/*
		Function: stripTags

		Arguments:
			properties - TBD

		Returns:
			void
	*/
	function stripTags($properties) {
		$tag_filter = new Zend_Filter_StripTags();
		foreach ($properties as $property) {
			if ($this->has($property)) {
				$this->$property = $tag_filter->filter($this->$property);
			}
		}
	}

	/*
		Function: has
			TBD

		Arguments:
			var - TBD

		Returns:
			boolean
	*/
	function has($var) {
		if (array_key_exists($var, get_object_vars($this))) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
		Function: getStartsWith
			returns an array of field names that start with $starts_with if any are present

		Arguments:
			starts_with - string to check for

		Returns:
			array of strings that are field names
	*/
	
	function getStartsWith($starts_with){
		$vars = get_object_vars($this);		
		$out = array();
		foreach($vars as $var => $val){
			if(!is_array($var)){				
				if(substr($var,0,strlen($starts_with)) == $starts_with){
					$out[] = $var;
				}
			}
		}
		return($out);
	}	
	

	/*
		Function: checkbox
			forces a boolean for a checkbox

		Arguments:
			var - checkbox value to evaluate
	*/
	function checkbox($var) {
		$out = false;
		if ($this->has($var)) {
			if ((boolean)$var) {
				$out = true;
			}
		}
		return $out;
	}

	/*
		Function: addValidator

		Arguments:
			field_name - The name of the POST or GET variable to validate.
			message - The message to return to the view if this field is invalid.
			type - The type of validation to perform.

		Returns: void
	*/
	function addValidator($field_name, $message = null, $type = null) {
		
		if (is_null($message)) {
				$message = "Please fill out the required field: ".$field_name;
		}
		
		if(is_null($type)){
			$type = "required";
		}
		
		$this->_validators[] = array("field_name" => $field_name, "message" => $message, "type" => $type);
	}

	/*
		Function: isValid
			Returns a boolean indicating if the validators set up for the request all passed.
			If one validator fails, the entire page is invalid and false is returned.

		Arguments: none

		Returns: boolean
	*/
	function isValid() {
		foreach ($this->_validators as $validator) {
			switch ($validator['type']) {
				case "required":
					if (!$this->has($validator['field_name']) || trim($this->{$validator['field_name']}) == "") {
						$this->_validation_errors[] = $validator['message'];
					}
					break;
			}
		}
		return (count($this->_validation_errors) === 0);
	}

}
