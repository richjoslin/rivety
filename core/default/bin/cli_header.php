<?php
/*
	File: cli_header.php
		This figures out where it is and then requires header.php, which sets up the rest of the app. 
		
	About: Author
		Jaybill McCarthy

	About: Contributors
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>
*/

$is_cli = true; // cli stuff will use a different log file since cli runs as a different user (for unknown reasons)
$location = "/core";
$cur_dir = dirname(__FILE__);
$basepath = substr($cur_dir, 0, strpos($cur_dir, $location));
require_once($basepath . '/header.php');
