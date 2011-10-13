<?php
/**
* Watermark Example #02
*
* This example shows the "tiling-watermark" feature, which tiles the watermark 
* image all over across the watermarked image. 
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
$i1 = asido::image('example.jpg', 'result_02.jpg');

/**
* Put a "tile" watermark image
*/
Asido::watermark($i1, 'watermark_02.png', ASIDO_WATERMARK_TILE);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>