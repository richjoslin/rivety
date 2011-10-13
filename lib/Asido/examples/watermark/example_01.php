<?php
/**
* Watermark Example #01
*
* This example shows how to use the watermarking gravity.
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
$i1 = asido::image('example.jpg', 'result_01.jpg');

/**
* Put a watermark image on every possible gravity location
*/
$watermark = 'watermark_01.png';
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_TOP_LEFT);
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_TOP_CENTER);
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_TOP_RIGHT);
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_MIDDLE_LEFT);
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_MIDDLE_CENTER);
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_MIDDLE_RIGHT);
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_BOTTOM_LEFT);
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_BOTTOM_CENTER);
Asido::watermark($i1, $watermark, ASIDO_WATERMARK_BOTTOM_RIGHT);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>