<?php
/**
* Resize Example #03
*
* This example shows how to do `stretching` resize
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
asido::driver('gd');

/**
* Create an Asido_Image object and provide the name of the source
* image, and the name with which you want to save the file
*/
$i1 = asido::image('example.png', 'result_03.png');

/**
* Resize the image by stretching it
*/
Asido::resize($i1, 500, 500, ASIDO_RESIZE_STRETCH);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>