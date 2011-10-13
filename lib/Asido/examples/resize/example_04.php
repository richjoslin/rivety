<?php
/**
* Resize Example #04
*
* This example shows the `fit` resize, which attempts to resize an image 
* (proportionally) only when it is bigger than a frame set by the provided width 
* and height - when it is do bigger, it is forced to "fit" inside that frame.
*
* @filesource
* @package Asido.Examples
* @subpackage Asido.Examples.Resize
*/

/////////////////////////////////////////////////////////////////////////////

/**
* Include the main Asido class
*/
include('./../../class.asido.php');

/**
* Set the correct driver: this depends on your local environment
*/
Asido::Driver('gd');

/**
* Create an Asido_Image object and provide the name of the source
* image, and the name with which you want to save the file
*/
$i1 = Asido::Image('example.png', 'result_04_1.png');
$i2 = Asido::Image('example.png', 'result_04_2.png');

/**
* Resize the image by fitting it inside the 800x800 frame: in
* fact it will not be resized because it is smaller
*/
Asido::Fit($i1, 800, 800);

/**
* Resize the image by fitting it inside the 400x400 frame: the image
* will do be resized by making it fit inside the 400x400 "mold"
*/
Asido::Fit($i2, 400, 400);

/**
* Save the result
*/
$i1->Save(ASIDO_OVERWRITE_ENABLED);
$i2->Save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>