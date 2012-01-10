<?php

/*

	File: images.php

	About: Author
		Jaybill McCarthy

	About: Contributors
		Rich Joslin

*/

// Caclulate basepath
$basepath =  substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos( $_SERVER['SCRIPT_FILENAME'], "/core/default/direct" ));
// Get config files
$registry_cache_file = $basepath . "/etc/cached_config.ini";
$registry_cache = parse_ini_file($registry_cache_file);
$config = parse_ini_file($basepath . "/etc/config.ini", true);
$max_height = 1000; // default max height. will be overriden by GET param
$max_width = (integer)$_GET["max_width"];	
$max_height = @(integer)$_GET["max_height"];
$username = $_GET["username"];
$type = $_GET["type"];
$filename = $_GET["filename"];
if (empty($filename)) $filename = 'missing.png';
$cache_dir = $config['application']['image_cache_dir'] . "/" . $username . "/";
$missing_image = $registry_cache['default.missing_image'];
$image = $registry_cache['default.upload_path'] . "/" . $username . "/" . $type . "/" . $filename;
$format = @$_GET["format"]; 
$crop = @(boolean)$_GET["crop"];

$missing = false;

try
{
	$missing = !file_exists($image);

	if ($missing)
	{
		$cache_dir = $config['application']['image_cache_dir'] . "/";
		$filename = "missing";
		$type = 'missing';
		$image = $missing_image;
		$missing = true;
	}
	else
	{
		switch ($type)
		{
			case "defaultprofile":
				$image = $basepath . $config['default_profile_fullimage_url'];
				$cache_dir = $config['application']['image_cache_dir'] . "/";
				break;
			default:
				if (strrchr($image, '/')) $filename = substr(strrchr($image, '/'), 1); // remove folder references
				else $filename = $image;
				break;
		}
	}
	$resized = $cache_dir . $max_width . '-' . $type . '-' . $filename;
	if ($crop) $resized = $cache_dir . $max_width . '-' . $type . '-' . $filename . "_cropped";
	$resized = $resized . "." . strtolower($format);
	if (!is_dir($cache_dir)) mkdir($cache_dir, 0777, true);
	if (!file_exists($resized))
	{
		if ($crop)
		{
			$size = getimagesize($image);
			$original_width = $size[0];
			$original_height = $size[1];
			if ($original_width > $original_height)
			{
				// this is a horizontal image
				$crop_width = $original_height;
				$crop_start_x = ($original_width / 2) - ($crop_width /2);
				$crop_start_y = 0;
			}
			else
			{
				// this is a vertical image
				$crop_width = $original_width;
				$crop_start_y = ($original_height / 2) - ($crop_width /2);
				$crop_start_x = 0;
			}
		}
		set_include_path(get_include_path() . PATH_SEPARATOR . $basepath . "/core/default/lib");
		set_include_path(get_include_path() . PATH_SEPARATOR . $config['application']['asido_path']);
		define('ASIDO_DIR', null);
		@include('class.asido.php'); 
		@asido::driver('gd_c');
		$i1 = @asido::image($image, $resized);
		if ($crop)
		{
			@asido::Crop($i1, $crop_start_x, $crop_start_y, $crop_width, $crop_width);
			@asido::width($i1, $max_width);
		}
		else
		{
			if ($max_height > 0) $use_height = $max_height;
			else $use_height = 1000;
			@asido::fit($i1, $max_width, $use_height);
		}
		if ($format == "jpg") @asido::convert($i1, 'image/jpeg');
		if ($format == "png") @asido::convert($i1, 'image/png');
		@$i1->save(ASIDO_OVERWRITE_ENABLED);
	}
	header("Content-Length: " . filesize($resized));
	if ($format == 'jpg' || $format == 'jpeg') header("Content-type: image/jpeg");
	elseif ($format == 'png') header("Content-type: image/png");
	else header("Content-type: image/jpeg"); // TODO: log an error when a format is passed in which is unrecognized
	readfile($resized);
}
catch (Exception $e)
{
	// TODO: Y U NO LOG SOMETHING HERE
}
exit();
