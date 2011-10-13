<?php
/**
* Crop Example #01
*
* This example shows ...
*
* @filesource
* @package Asido.Examples
* @subpackage Asido.Examples.Crop
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
$i1 = asido::image('example.png', 'result_01.jpg');

/**
* Crop a portion of the image from the upper left corner
*/
Asido::Crop($i1, 0, 0, 300, 300);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>