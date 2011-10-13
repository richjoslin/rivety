<?php
/**
* Convert Example #02
*
* This example shows how to convert an image explicitly declaring the type of 
* the image disregarding the extension of the "result" image.
*
* @filesource
* @package Asido.Examples
* @subpackage Asido.Examples.Convert
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
* Save the result as GIF nevertheless we set the result name to be a JPEG one
*/
Asido::convert($i1, 'image/gif');

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>