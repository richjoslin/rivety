<?php
/*
	File: cli_header.php
		This figures out where it is and then requires header.php, which sets up the rest of the app. 
		
	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>
*/

$location = "/modules";
$cur_dir = dirname(__FILE__);
$basepath = substr($cur_dir, 0, strpos($cur_dir, $location));
require_once($basepath.'/header.php');
