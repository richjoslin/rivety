<?php

/*
	Class: RivetyCore_Translate

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_Translate {

	/* Group: Static Methods */

	/*
		Function: translate
	*/
	static function translate($locale_code, $module_name, $key, $replace = null, $do_translation = true) {

// DON'T EVER LEAVE THIS UNCOMMENTED
// ob_clean();
// can be useful for debugging since using dd() will dump out into the existing markup and be hard to see
// but this clears out all the other markup so the debug data can be seen clearly

		$translation = $key;
		if ($do_translation) {
			if (RivetyCore_Registry::get('enable_localization') == '1'
			 && !is_null($module_name) && trim($module_name) != ""
			 && !is_null($key) && trim($key) != "") {
				$locale_code = RivetyCore_Translate::cleanZendLocaleCode($locale_code);

				// TODO: account for core rivety module
				$path_to_csv = RivetyCore_Registry::get('basepath')."/modules/".$module_name."/languages/".$locale_code.".csv";

				if (file_exists($path_to_csv)) {
					try {
						$translate = new Zend_Translate("csv", $path_to_csv, $locale_code, array('delimiter' => ","));
						$translation = $translate->_($key);
						// this next bit will populate the locale file with untranslated terms
						// so it's easier for someone to go through and translate them
						if (RivetyCore_Registry::get('auto_populate_language_files') == '1') {
							if (!$translate->isTranslated($key, true, $locale_code)) {
								$key_no_quotes = str_replace('"', '&quot;', $key);
								$str = '"'.$key_no_quotes.'","'.$key_no_quotes.'"'."\n";
								file_put_contents($path_to_csv, $str, FILE_APPEND);
							}
						}
					} catch (Exception $e) {
						$translation = $key;
					}
				} else {
					// create the file
					file_put_contents($path_to_csv, $key.','.$key);
				}
			}
		}
		$output = "";
		if (is_null($replace)) {
			// no replace, no sprintf
			$output = $translation;
		} else {
			if (is_array($replace)) {
				if (count($replace) > 1) {
					// there are multiple indices, use vsprintf
					$output = vsprintf($translation, $replace);
				} else {
					// there's only one index, use the cheaper sprintf instead
					$output = sprintf($translation, $replace[0]);
				}
			} else {
				// $replace is not an array, so try using it straight
				$output = sprintf($translation, $replace);
			}
		}
		return $output;
	}

	/*
		Function: cleanZendLocaleCode
			Prepares a locale code string for use with the Zend_Translate or Zend_Locale classes.

		Returns: string
	*/
	static function cleanZendLocaleCode($locale_code) {
		// Zend_Locale strictly expects underscores and not dashes
		$output = str_replace("-", "_", $locale_code);
		// if there is a second half, capitalize it
		// Zend_Locale strictly expects case-sensitive codes
		// with the first part lowercase and the second part uppercase
		$output = strtolower($output);
		$output = preg_replace("/_(\w+)/e", "'_'.strtoupper('\\1')", $output);
		return $output;
	}

	/*
		Function: validateLocaleCode
			Uses a regular expression to see if the supplied locale code is a valid one.
			Considers *any* locale code format as valid, not just Zend-friendly.

		Returns: boolean
	*/
	static function validateLocaleCode($code) {
		// matches: en, en-US, en-us, en_US, en_us
		$matchcount = preg_match("^\D{2,3}(?:[_-]{1}\D{2})?^", $code);
		if ($matchcount === false || $matchcount == 0) {
			return false;
		} else {
			return true;
		}
	}

}
