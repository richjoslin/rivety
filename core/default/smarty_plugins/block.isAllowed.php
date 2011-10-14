<?php
/*
	Title: smarty_block_t
		Identical to <smarty_block_translate>.

	About: Author
		Rich Joslin

	About: License
		<http://communit.as/docs/license>

	Function: smarty_block_translate
		Identical to <smarty_block_translate>.

	Arguments:
		$params - An array of variables. (TBD)
		$content - This is everything between the block start and end tags in the template.
		&$smarty - TBD
		&$repeat - TBD

	Params:
		TBD

	Returns:
		A translated string.

	Execution Example:
		(begin example)
			<!-- as used in a Smarty view template -->
			{t}This text will be translated based on the user's chosen locale.{/t}
		(end example)

	About: See Also
		- <smarty_block_translate>
		- <RivetyCore_Translate/translate>
*/
function smarty_block_isAllowed($params, $content, $smarty, $repeat) {
	$tpl_vars = $smarty->_tpl_vars;
    // only output on the closing tag
    if (!$repeat) {
        if (isset($content) && isset($params['controller'])) {        	
        	if(RivetyCore_ResourceCheck::isAllowed($params['resource'],$params['module'],$params['username'],$params['controller'])){
        		return $content;
        	} else {
        		return null;
        	}
		}
        if (isset($content) && !isset($params['controller'])) {        	
        	if(RivetyCore_ResourceCheck::isAllowed($params['resource'],$params['module'],$params['username'])){
        		return $content;
        	} else {
        		return null;
        	}
		}
	}
}
