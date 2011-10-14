<?php

/*
	Title: tag_cloud

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	Function: smarty_function_tag_cloud
		Prints a 'tag cloud' generated from the passed parameters.

	Arguments:
		$params - An array of variables.
		&$smarty - TBD

	Params:
		tags (associative array) - An array of tags and the totals for each tag. (see "Tag Array Example" below)
		base_url (string) - The base portion of the URL to which the tag will be appended.
		min_font_size (optional integer) - The font size for the smallest tag in the cloud.
		max_font_size (optional integer) - The font size for the largest tag in the cloud.
		class - optional - string - The CSS class to assign to the outer tag cloud HTML element. Defaults to 'tag_cloud'.
		levels (optional) - An integer representing the number of ranks to divide into tags. Defaults to 6.
		item_type (optional) - string - Tells the view what to output as the word for what the tags represent (e.g.: items, books, shoes, etc). Defaults to 'item(s)'.
		limit optional integer - An integer representing the maximum number of tags to include in the cloud. Defaults to 20.

	Returns:
		A string containing an HTML ordered list (<ol>) full of tags.

	Tag Array Example:
		(begin example)
			$tags = Array();
			$tags['horse'] = 22;
			$tags['cow'] = 12;
			$tags['chicken'] = 30;
			$tags['goat'] = 23;
		(end example)

	Execution Example:
		(begin example)
			<!-- as used in a Smarty view template -->
			{tag_cloud tags=$my_tags base_url='/search.php?q='}
		(end example)

*/
function smarty_function_tag_cloud($params, &$smarty) {
	
	$tag_totals = $params['tags'];
	$base_url = $params['base_url'];
	
	if (is_null($params['levels'])) {
		$levels = 6;
	} else {
		$levels = $params['levels'];
	}
	
	if (is_null($params['limit'])) {
		$limit = 20;
	} else {
		$limit = $params['limit'];
	}
	
	if (is_null($params['item_type'])) {
		$item_type = 'item(s)';
	} else {
		$item_type = $params['item_type'];
	}
	
	if (is_null($params['min_font_size'])) {
		$min_font_size = 12;
	} else {
		$min_font_size = $params['min_font_size'];
	}
	
	if (is_null($params['max_font_size'])) {
		$max_font_size = 30;
	} else {
		$max_font_size = $params['max_font_size'];
	}

	if (is_null($params['id'])) {
		$id = "tag-cloud";
	} else {
		$id = $params['id'];
	}

	if (is_null($params['class'])) {
		$class = "tag-cloud";
	} else {
		$class = $params['class'];
	}
	
	if (($count = count($tag_totals)) > $limit) {
		arsort($tag_totals, SORT_NUMERIC);
		$tag_totals = array_slice($tag_totals, 0, $limit);
	}
	
	$max = max($tag_totals); $min = min($tag_totals);
	$ratio = ($max - $min)/$levels;
	$tags = array();
	foreach ($tag_totals as $tag => $freq) {
		$tmp_tag = $freq == $max ? $levels : ceil(($freq - $min + 1) / $ratio);	
		$tags[$tag] = (integer) $tmp_tag;
	}
	
	ksort($tags);
	
	$cloud_html = '';
	$cloud_tags = array(); 
	
	foreach ($tags as $tag => $rank) {
		$display_tag = $instr = str_replace("-"," ",trim($tag));	
		$cloud_tags[] = '<li class="size'.$rank.'"><span>'
		.$tag_totals[$tag].' '.$item_type.' tagged </span><a title="'
		.$tag_totals[$tag]. ' ' .$item_type. ' tagged with ' .$display_tag. '" class="tag" href="'
		.$base_url.$tag	. '">'.$display_tag.'</a> </li>';
	}

	if (!empty($cloud_tags)) { // make sure we have tags before outputting the markup	
		$cloud_html = '<ol class="tag-cloud">'.join("", $cloud_tags)."</ol>";
	}

	return $cloud_html;
}
