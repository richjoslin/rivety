<?php
/**
* Convert Example #01
*
* This example shows how the convert works without explicitly declaring it but 
* only using the extension of the filename of the "result" image (result_01.gif)
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
$i1 = asido::image('example.png', 'result_01.gif');

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>