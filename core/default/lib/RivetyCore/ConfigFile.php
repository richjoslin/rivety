<?php

/*
	Class: RivetyCore_ConfigFile

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_ConfigFile {

	/* Group: Static Methods */

	/*
		Function: makeSection
			Prepares a string formatted for use in a config file. Does not write to any files, just returns a formatted string.

		Arguments:
			section_name - The name of the section to write to the config file.
			title - The title to write to the config file.
			comment - A comment to write to the config file.
			keyvals - An array of key-value pairs to write to the config file as configuration settings.

		Returns:
			A formatted string that can be written to a config file.
	*/
	static function makeSection($section_name, $title, $comment, $keyvals) {
		$outstr = "\n;=========================================================================\n";
		$outstr .= "; " . $title . "\n";
		$outstr .= ";=========================================================================\n";
		$comment = "\n; " . wordwrap($comment, 75, "\n; ");
		$outstr .= $comment . "\n";
		$outstr .= "\n[" . $section_name . "]\n";
		foreach ($keyvals as $key => $val) {
			$outstr .= $key. " = \"" . $val ."\"\n";
		}
		return $outstr . "\n";
	}
}
