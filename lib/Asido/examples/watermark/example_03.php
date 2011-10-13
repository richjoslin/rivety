<?php
/**
* Watermark Example #03
*
* This example shows how the watermark scaling works. This feature is turned on 
* by default and it is very handy when the watermark image is considerable larger 
* then the image that is about to be watermarked.
*
* @filesource
* @package Asido.Examples
* @subpackage Asido.Examples.Watermark
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
$i1 = asido::image('example.jpg', 'result_03.jpg');

/**
* Put the watermark with the scaling feature enabled
*/
Asido::watermark($i1, 'watermark_03.png', ASIDO_WATERMARK_BOTTOM_RIGHT, ASIDO_WATERMARK_SCALABLE_ENABLED);

/**
* Put the watermark with the scaling feature disabled
*/
Asido::watermark($i1, 'watermark_03.png', ASIDO_WATERMARK_CENTER, ASIDO_WATERMARK_SCALABLE_DISABLED);

/**
* Save the result
*/
$i1->save(ASIDO_OVERWRITE_ENABLED);

/////////////////////////////////////////////////////////////////////////////

?>