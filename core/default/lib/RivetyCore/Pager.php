<?php

/*
	Class: RivetyCore_Pager

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>
*/
class RivetyCore_Pager {

	/* Group: Static Methods */

	/*
		Function: getPage
			Pulls a certain range of items from an array based on a certain page size.

		Arguments:
			array_to_page
			per_page
			page (optional) - Default is 1.

		Returns:
			An array of items within the range determined by the page size (per_page) and page number (page).
	*/
	static function getPage($array_to_page, $per_page, $page = 1) {
		$chunked_array = array_chunk($array_to_page, $per_page);
		return($chunked_array[$page]);
	}
}
