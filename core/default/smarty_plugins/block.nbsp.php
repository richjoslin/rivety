<?php

/*
	Title: smarty_block_nbsp

	About: Author
		Tyler Vigeant

	About: License
		<http://rivety.com/docs/license>

	Function: smarty_block_nbsp

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
			{nbsp}This text will have its spaces replaced with &nbsp;{/nbsp}
		(end example)
	
	About: See Also
		- This should always be used outside of the {t}{/t} tags.
*/
function smarty_block_nbsp($params, $content, $smarty, $repeat) {
	return str_replace(' ', '&nbsp;', $content);
}
