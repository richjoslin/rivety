<?php
/**
* Watermark Example #05
*
* This example shows the result when using non-transparent images like JPEGs, or 
* using files with non-alpha transparency like GIFs. 
*
* @filesource
* @package Asido.Examples
* @subpackage Asido.Examples.Watermark
*/

/////////////////////////////////////////////////////////////////////////////

/**
* Include the main Asido class
*/
include('./../../class.asido.php');

/**
* Set the correct driver: this depends on your local environment
*/
asido::driver('gd');

/**
* Create an Asido_Image object and provide the name of the source
* image, and the name with which you want to save the file
*/
$i1 = asido::image('example.jpg', 'result_05.jpg');

/**
* Put a JPEG watermark image
*/
Asido::watermark($i1, 'watermark_05.jpg', ASIDO_WATERMARK_TOP_LEFT);

/**
* Put a GIF watermark image
*/
Asido::watermark($i1, 'watermark_05.gif', ASIDO_WATERMARK_BOTTOM_RIGHT);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>