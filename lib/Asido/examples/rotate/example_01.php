<?php
/**
* Rotate Example #01
*
* This example shows how to rotate an image by 90 degrees.
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
$i1 = asido::image('example.png', 'result_01.png');

/**
* Rotates the image by 90 degrees
*/
Asido::Rotate($i1, 90);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>