<?php

/*
	Class: RivetyCore_Filesystem

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_Filesystem {

	/* Group: Static Methods */

	/*
		Function: getPath
			Concatenates the provided username into a valid path for either uploads or user cache.

		Arguments:
			pathtype - A string, either 'userupload' or 'usercache', to decide which path to return.
			username - The username to concatenate into the path.

		Returns:
			A string containing the desired path containing the given username.
	*/
	static function getPath($pathtype, $username) {
		// TODO - refactor this into registry or database or something
		$value = Zend_Registry::get('basepath');
		switch ($pathtype) {
			case 'userupload':
				$value .= "/uploads/" . $username . "/original";
				break;
			case 'usercache':
				$value .= "/tmp/image_cache/" . $username;
				break;
		}
		return $value;
	}

	/*
		Function: SureRemoveDir
			TBD

		Arguments:
			dir - TBD
			DeleteMe - TBD
	*/
	static function SureRemoveDir($dir, $DeleteMe) {
	    if (!$dh = @opendir($dir)) {
	    	return;
	    }
	    while (false !== ($obj = readdir($dh))) {
	        if($obj == '.' || $obj == '..') continue;
	        if (!@unlink($dir . '/' . $obj)) {
	        	SureRemoveDir($dir . '/' . $obj, true);
	        }
	    }
	    closedir($dh);
	    if ($DeleteMe) {
	        @rmdir($dir);
	    }
	}

}
