<?php
/**
* Rotate Example #02
*
* This example shows how to do a custom rotate by 30 degrees, and we are 
* filling the blank areas left by the rotate with a nice green color.
*
* @filesource
* @package Asido.Examples
* @subpackage Asido.Examples.Rotate
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
$i1 = asido::image('example.png', 'result_02.png');

/**
* Rotates the image by 30 degrees
*/
Asido::Rotate($i1, 30, Asido::Color(39, 107, 20));

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>