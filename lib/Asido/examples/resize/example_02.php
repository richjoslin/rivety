<?php
/**
* Resize Example #02
*
* This example shows how the proportional resize only by one dimension (width) works
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
$i1 = asido::image('example.png', 'result_02.png');

/**
* Resize the image proportionally only by setting only the width, and the height will be corrected accordingly
*/
Asido::width($i1, 600);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>