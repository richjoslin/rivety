<?php
/**
 * Rivety
 * @copyright (C)2008 Jaybill McCarthy, All Rights Reserved.
 * @category rivety
 * @package rivety
 * @author Jaybill McCarthy
 * @link http://rivety.com Rivety
 * @license http://rivety.com/docs/license License
 */

/**
 *
 * @package rivety
 * @subpackage core_lib
 * @license http://rivety.com/docs/license License 
 */
 
class Constants {

	function Constants(){
		define('DB_DATETIME_FORMAT',"Y-m-d H:i:s");
		define('DB_DATE_FORMAT',"Y-m-d");
		
		define('URL_REGEX','@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@');
		define('URL_REGEX_REPLACE','<a href="$1">$1</a>');
		
	}

}