<?php
/**
* Watermark Example #04
*
* This example shows how the watermark scaling factor works.
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
$i1 = asido::image('example.jpg', 'result_04.jpg');

/**
* Put the watermark with the scaling factor 0.66
*/
Asido::watermark($i1, 'watermark_04.png', ASIDO_WATERMARK_TOP_LEFT, ASIDO_WATERMARK_SCALABLE_ENABLED, 0.66);

/**
* Put the watermark with the scaling factor 0.75
*/
Asido::watermark($i1, 'watermark_04.png', ASIDO_WATERMARK_BOTTOM_RIGHT, ASIDO_WATERMARK_SCALABLE_ENABLED, 0.75);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>