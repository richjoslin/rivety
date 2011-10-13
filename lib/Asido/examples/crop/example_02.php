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
$i1 = asido::image('example.png', 'result_02.jpg');

/**
* Copy the image with over the resulting image
*/
Asido::copy($i1, 'copy_01.png', 15, 15);

/**
* Copy an image using negative coordinates
*/
Asido::copy($i1, 'copy_02.jpg', -35, -35);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>