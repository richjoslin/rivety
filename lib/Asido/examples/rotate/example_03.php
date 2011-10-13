<?php
/**
* Rotate Example #03
*
* This example shows how to flip an image. Flip means to do a vertical mirror. 
* We are using the `gd_hack` driver since the flipping and flopping is not 
* supported by the regular `gd` driver.
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
asido::driver('gd_hack');

/**
* Create an Asido_Image object and provide the name of the source
* image, and the name with which you want to save the file
*/
$i1 = asido::image('example.png', 'result_03.png');

/**
* Flip it ;)
*/
Asido::Flip($i1);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>