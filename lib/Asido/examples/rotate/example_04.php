<?php
/**
* Rotate Example #04
*
* This example shows how to flop an image. Flop means to do a horizontal mirror.
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
$i1 = asido::image('example.png', 'result_04.png');

/**
* Flop it ;)
*/
Asido::Flop($i1);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>