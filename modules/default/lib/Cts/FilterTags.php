<?php

/*
	Class: Cts_FilterTags

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		Zend_Filter_Interface
*/
class Cts_FilterTags implements Zend_Filter_Interface {

	/* Group: Instance Methods */

	/*
		Function: filter
			TBD

		Arguments:
			instr - TBD

		Returns:
			A string.
	*/
	function filter($instr) {
		$outstr = null;
		$filterChain = new Zend_Filter();
		$filterChain->addFilter(new Zend_Filter_StringToLower())
        			->addFilter(new Zend_Filter_StripTags())
        			->addFilter(new Zend_Filter_StringTrim());

		$instr = $filterChain->filter($instr);
		$instr = str_replace(" ", "-", trim($instr));
		preg_match_all("/^[a-z0-9\-\\\$]{1,}$/", $instr, $parts);

		foreach ($parts as $part) {
			$outstr .= $part[0];
		}

		return $outstr;
	}	
}
