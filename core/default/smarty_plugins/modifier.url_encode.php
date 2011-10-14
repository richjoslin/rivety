
<?php
/**
 * Smarty shared plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Function: smarty_url_encode
 * Purpose:  Encodes a string to be used in a url environment
 * Example: "Jason, the maker of this script (Oh yea..), was here!" TO "Jason%2C+the+maker+of+this+script+%28Oh+yea..%29%2C+was+here%21"
 * @author Jason Strese <Jason dot Strese at gmail dot com>
 * @param string
 * @return string
 */
function smarty_modifier_url_encode($string)
{
      return urlencode($string);
}

