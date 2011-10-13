<?php
/**
* Resize Example #05
*
* This example shows the `passepartout` resize which resizes the image 
* proportionally, but the result has the proportions of the provided width and 
* height with the blank areas filled with the provided color. In the case we are 
* resizing a 640x480 image by making it fit inside a square frame 300x300 and 
* using a nice green color as background. If the color argument is omitted, then 
* "white" is used to fill the blank areas. This is very handy when you want all 
* the resulting images to fit inside some frame without stretching them if the 
* proportions of the image and the frame do not match
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
$i1 = Asido::Image('example.png', 'result_05.png');

/**
* Resize the image by putting it inside a square frame (300x300) with `rgb(177,77,37)` as background.
*/
Asido::Frame($i1, 300, 300, Asido::Color(39, 107, 20));

/**
* Save the result
*/
$i1->Save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>