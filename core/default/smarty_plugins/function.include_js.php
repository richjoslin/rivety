<?php

/*
	Title: include_js

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	Function: smarty_function_include_js
		Prints script tags based on a space-delimited list of URLs passed in.
		When setting the urls array, any tabs or carriage returns are stripped out and replaced with spaces so the split will work.
		So format it any way you want (theoretically).

	Arguments:
		$params - An array of variables.
		&$smarty - TBD

	Params:
		TBD

	Returns:
		A string containing one or more script tags.

	Header Tag Example:
		(begin example)
			{capture name=js_urls}
				{$theme_url}/modules/dealers/js/admin/index.js
				{$theme_url}/modules/dealers/js/foo/bar.js
			{/capture}
			{include file="file:$theme_global/_header.tpl" current="dealers_admin_index" js_urls=$smarty.capture.js_urls}
		(end example)

	Execution Example:
		(begin example)
			<!-- as used in a Smarty view template -->
			{include_js urls=$js_urls}
		(end example)
*/
function smarty_function_include_js($params, &$smarty) {
	$urls = $params['urls'];
	$urls = str_replace(array("\t", "\n"), array(" ", " "), $urls);
	while (stripos($urls, "  ") !== false) {
		$urls = str_replace("  ", " ", $urls);
	}
	$urls = split(" ", $urls);
	$output = "";
	foreach ($urls as $url) {
		if (trim($url) != "") {
			$output .= "<script type=\"text/javascript\" src=\"".trim($url)."\"></script>\n";
		}
	}
	return $output;
}
