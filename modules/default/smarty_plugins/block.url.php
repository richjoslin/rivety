<?php
/*
	Title: smarty_block_url
		Makes predetermined modifications to a URL using the Cts_Common::urlFilter method.

	About: Author
		Rich Joslin

	About: License
		<http://communit.as/docs/license>

	Function: smarty_block_url
		Makes predetermined modifications to a URL using the Cts_Common::urlFilter method.

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
			{url}/user{/url}
			<!-- will output /en-US/user if there is a locale view variable,
				or /user if there is no locale view variable set -->
		(end example)

	About: See Also
		- <Cts_Common/urlFilter>
*/
function smarty_block_url($params, $content, $smarty, $repeat) {
	$tpl_vars = $smarty->_tpl_vars;
    // only output on the closing tag
    if (!$repeat) {
        if (isset($content)) {
			$urlparams = array();
			$locale_code = $tpl_vars['locale_code'];
			$request_locale = $tpl_vars['request_locale'];
			if($request_locale && $locale_code != $request_locale) {
				$urlparams['locale_code'] = strtolower($request_locale);
			} elseif (!is_null($locale_code) && Cts_Translate::validateLocaleCode($locale_code)) {
				$urlparams['locale_code'] = strtolower($locale_code);
			}
			$url_filter = new Cts_Url_Filter();
			return $url_filter->filter($content, $urlparams);
		}
	}
}
