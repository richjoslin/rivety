<?php

/*
	Title: include_css

	About: Author
		Rich Joslin

	About: License
		<http://communit.as/docs/license>

	Function: smarty_function_include_css
		Prints css include tags based on a space-delimited list of URLs passed in.
		When setting the urls array, any tabs or carriage returns are stripped out and replaced with spaces so the split will work.
		So format it any way you want (theoretically).

	Arguments:
		$params - An array of variables.
		&$smarty - TBD

	Params:
		TBD

	Returns:
		A string containing one or more css include tags.

	Header Tag Example:
		(begin example)
			{capture name=css_urls}
				{$theme_url}/modules/dealers/css/admin/index.css
				{$theme_url}/modules/dealers/css/foo/bar.css
			{/capture}
			{include file="file:$theme_global/_header.tpl" css_urls=$smarty.capture.css_urls}
		(end example)

	Execution Example:
		(begin example)
			<!-- as used in a Smarty view template -->
			{include_css urls=$css_urls}
		(end example)
*/
function smarty_function_include_css($params, &$smarty) {
	$urls = $params['urls'];
	$urls = str_replace(array("\t", "\n"), array(" ", " "), $urls);
	while (stripos($urls, "  ") !== false) {
		$urls = str_replace("  ", " ", $urls);
	}
	$urls = split(" ", $urls);
	$output = "";
	foreach ($urls as $url) {
		if (trim($url) != "") {
			$output .= "<link rel=\"stylesheet\" href=\"".trim($url)."\" type=\"text/css\" media=\"screen, projection\" />\n";

		}
	}
	return $output;
}
