<?php
/**
 * communit.as
 * @copyright (C)2008 Jaybill McCarthy, All Rights Reserved.
 * @category communitas
 * @package communitas
 * @author Jaybill McCarthy
 * @link http://communit.as communit.as
 * @license http://communit.as/docs/license License
 */

/**
 *
 * @package communitas
 * @subpackage core_lib
 * @license http://communit.as/docs/license License 
 */
 
class Constants {

	function Constants(){
		define('DB_DATETIME_FORMAT',"Y-m-d H:i:s");
		define('DB_DATE_FORMAT',"Y-m-d");
		
		define('URL_REGEX','@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@');
		define('URL_REGEX_REPLACE','<a href="$1">$1</a>');
		
	}

}