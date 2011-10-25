<?php

/*
	Title: smarty_block_translate
		Translates a string based on the current user's chosen locale.

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	Function: smarty_block_translate
		Translates a string based on the current user's chosen locale. Uses RivetyCore_Translate::translate.

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
			{translate}This text will be translated based on the user's chosen locale.{/translate}
		(end example)

	About: See Also
		- <RivetyCore_Translate/translate>
*/
function smarty_block_translate($params, $content, $smarty, $repeat) {
	$tpl_vars = $smarty->_tpl_vars;
    // only output on the closing tag
    if (!$repeat) {
        if (isset($content)) {
			$do_translation = true;
			if ($smarty->_tpl_vars['isAdminController'] && RivetyCore_Registry::get('enable_admin_localization', 'default') == '0') {
				$do_translation = false;
			}
			if ($params['replace']) {
				return RivetyCore_Translate::translate($tpl_vars['locale_code'], "default", $content, $params['replace'], $do_translation);
			} else {
				return RivetyCore_Translate::translate($tpl_vars['locale_code'], "default", $content, null, $do_translation);
			}
		}
	}
}
