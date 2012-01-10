<?php

/*
	Class: RivetyCore_Common

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_Common
{

	/* Group: Static Methods */

	/*
		Function: getTimeZonesArray
			Try to get a list of timezones automatically. If we're using PHP < 5.1.3, just pass out something manual, as timezone_name_from_abbr doesn't exist yet.

		Arguments:
			choose_one - A string to be used for the default option. "Choose one..." or something along that line.

		Returns:
			timezones - array continaing a list of timezones suitable for an html select tag
	*/
	static function getTimeZonesArray($choose_one = null)
	{
		$timezones = array();

		if (function_exists("timezone_name_from_abbr"))
		{
			if (!is_null($choose_one))
			{
				$timezones[null] = $choose_one;
			}
			for ($x = -12; $x <= 14; $x++)
			{
				$tz_name = timezone_name_from_abbr("", ($x * 60 * 60), 0);
				if ($tz_name != "")
				{
					$timezones[$tz_name] = "[GMT";
					if ($x > 0)
					{
						$timezones[$tz_name] .= " +" . $x;
					}
					elseif ($x < 0)
					{
						$timezones[$tz_name] .= " " . $x;
					}
					$timezones[$tz_name] .= "] ";
					$timezones[$tz_name] .= str_replace("_", " ", $tz_name);
				}
			}
		}
		else
		{
			$timezones = array (
				'Pacific/Apia' => '[GMT -11] Pacific/Apia',
				'Pacific/Honolulu' => '[GMT -10] Pacific/Honolulu',
				'America/Anchorage' => '[GMT -9] America/Anchorage',
				'America/Los_Angeles' => '[GMT -8] America/Los Angeles',
				'America/Denver' => '[GMT -7] America/Denver',
				'America/Chicago' => '[GMT -6] America/Chicago',
				'America/New_York' => '[GMT -5] America/New York',
				'America/Halifax' => '[GMT -4] America/Halifax',
				'America/Sao_Paulo' => '[GMT -3] America/Sao Paulo',
				'Atlantic/Azores' => '[GMT -1] Atlantic/Azores',
				'Europe/London' => '[GMT] Europe/London',
				'Europe/Paris' => '[GMT +1] Europe/Paris',
				'Europe/Helsinki' => '[GMT +2] Europe/Helsinki',
				'Europe/Moscow' => '[GMT +3] Europe/Moscow',
				'Asia/Dubai' => '[GMT +4] Asia/Dubai',
				'Asia/Karachi' => '[GMT +5] Asia/Karachi',
				'Asia/Krasnoyarsk' => '[GMT +7] Asia/Krasnoyarsk',
				'Asia/Tokyo' => '[GMT +9] Asia/Tokyo',
				'Australia/Melbourne' => '[GMT +10] Australia/Melbourne',
				'Pacific/Auckland' => '[GMT +12] Pacific/Auckland',
			);
		}
		return $timezones;
	}

	// /*
	//	Function: calculateYearsOld
	//		Calculates the number of years between a birthdate and now.
	//
	//	Arguments:
	//		timestamp - A unix timestamp representing a person's date of birth.
	//
	//	Returns:
	//		An age in years as an integer.
	// */
	// static function calculateYearsOld($timestamp) {
	//	$bday = date("j", $timestamp);
	//	$bmonth = date("n", $timestamp);
	//	$byear = date("Y", $timestamp);
	//	$years = date("Y") - intval($byear);
	//	//If birthday is after today, he/she has one year old less
	//	$day   = str_pad(intval($bday), 2, "0", STR_PAD_LEFT);
	//	$month = str_pad(intval($bmonth), 2, "0", STR_PAD_LEFT);
	//	if(intval("$month$day") > intval(date("md"))) {
	//		$years -= 1;
	//	}
	//	return $years;
	// }

	// /*
	//	Function: calculateAstroSign
	//		Calculates a star sign based on a birth date. Yeah, I know. You probably won't ever need this.
	//		One of our early clients did and it's awesome code so we left it.
	//
	//	Arguments:
	//		timestamp - The birth date to calculate as a unix timestamp.
	//
	//	Returns:
	//		The name of the star sign (string).
	// */
	// static function calculateAstroSign($timestamp) {
	//
	//	$Day = date("j", $timestamp);
	//	$Month = date("n", $timestamp);
	//
	//	// Calculate the number of days
	//
	//	$Offset = floor(((((mktime(0, 0, 0, $Month, $Day, 0) - mktime(0, 0, 0, 1, 0, 0))) / 60) / 60) / 24);
	//
	//	// Lookup the Sign based on the
	//	// number of days past Jan 1
	//
	//	if ($Offset >= 0) $Sign = "capricorn";
	//	if ($Offset >= 20) $Sign = "aquarius";
	//	if ($Offset >= 50) $Sign = "pisces";
	//	if ($Offset >= 81) $Sign = "aries";
	//	if ($Offset >= 110) $Sign = "taurus";
	//	if ($Offset >= 141) $Sign = "gemini";
	//	if ($Offset >= 173) $Sign = "cancer";
	//	if ($Offset >= 204) $Sign = "leo";
	//	if ($Offset >= 235) $Sign = "virgo";
	//	if ($Offset >= 266) $Sign = "libra";
	//	if ($Offset >= 296) $Sign = "scorpio";
	//	if ($Offset >= 327) $Sign = "sagittarius";
	//	if ($Offset >= 357) $Sign = "capricorn";
	//
	//	return $Sign;
	// }

	// /*
	//	Function: getSignArray
	//		Gets a list of astrological signs.
	//
	//	Returns:
	//		An array of star signs.
	// */
	// static function getSignArray() {
	//	$just_signs = array(
	//		"aquarius",
	//		"pisces",
	//		"aries",
	//		"taurus",
	//		"gemini",
	//		"cancer",
	//		"leo",
	//		"virgo",
	//		"libra",
	//		"scorpio",
	//		"sagittarius",
	//		"capricorn"
	//	);
	//	$signs = array();
	//	$signs[null] = "Any";
	//	foreach ($just_signs as $sign) {
	//		$signs[$sign] = ucwords($sign);
	//	}
	//	return $signs;
	// }

	// /*
	//	Function: getSignBetween
	//		TBD
	//
	//	Arguments:
	//		sign - TBD
	//		colname (optional) - TBD
	//
	//	Returns:
	//		TBD
	// */
	// static function getSignBetween($sign, $colname = "birthday_day") {
	//	$signs = array();
	//	$signs['capricorn']['start']	= 0;
	//	$signs['capricorn']['end']		= 19;
	//
	//	$signs['capricorn_2']['start']	= 357;
	//	$signs['capricorn_2']['end']	= 365;
	//
	//	$signs['aquarius']['start']	= 20;
	//	$signs['aquarius']['end']		= 49;
	//
	//	$signs['pisces']['start']		= 50;
	//	$signs['pisces']['end']		= 80;
	//
	//	$signs['aries']['start']		= 81;
	//	$signs['aries']['end']			= 109;
	//
	//	$signs['taurus']['start']		= 110;
	//	$signs['taurus']['end']		= 140;
	//
	//	$signs['gemini']['start']		= 141;
	//	$signs['gemini']['end']		= 172;
	//
	//	$signs['cancer']['start']		= 173;
	//	$signs['cancer']['end']		= 203;
	//
	//	$signs['leo']['start']			= 204;
	//	$signs['leo']['end']			= 234;
	//
	//	$signs['virgo']['start']		= 235;
	//	$signs['virgo']['end']			= 265;
	//
	//	$signs['libra']['start']		= 266;
	//	$signs['libra']['end']			= 295;
	//
	//	$signs['libra']['start']		= 296;
	//	$signs['libra']['end']			= 326;
	//
	//	$signs['scorpio']['start']		= 327;
	//	$signs['scorpio']['end']		= 356;
	//
	//	$between = $colname . " BETWEEN ";
	//
	//	if ($sign == "capricorn") {
	//		$between .= $signs['capricorn']['start'] . " and " . $signs['capricorn']['end'];
	//		$between .= " or " . $colname . " between ";
	//		$between .= $signs['capricorn_2']['start'] . " and " . $signs['capricorn_2']['end'];
	//	} else {
	//		$between .= $signs[$sign]['start'] . " and " . $signs[$sign]['end'];
	//	}
	//
	//	return $between;
	// }

	// /*
	//	Function: br2nl
	//		Converts XHTML <br /> tags to newline characters (chr 32).
	//
	//	Arguments:
	//		string - An XHTML string to convert.
	//
	//	Returns:
	//		A converted string.
	// */
	// static function br2nl($string) {
	//	return str_replace('<br />', chr(32), $string);
	// }

	// /*
	//	Function: makeParagraphs
	//		TBD
	//
	//	Arguments:
	//		string - TBD
	//
	//	Returns:
	//		TBD
	// */
	// static function makeParagraphs($string) {
	//	$lines = explode("\n", $string);
	//		$text = "";
	//		foreach ($lines as $line) {
	//			if (strlen($line) > 0) {
	//				if (strpos($line, "<h") === false) {
	//					$text .= "\n<p>" . $line . "</p>\n";
	//				} else {
	//					$text .= "\n" . $line . "\n";
	//				}
	//			}
	//		}
	//	return $text;
	// }

	/*
		Function: sortDataSet
			Sorts a set of data (multidimensional array).

		Arguments:
			&$dataset - A multidimensional array (data set) to sort.
				Subsequent arguments follow the argument order of array_multisort(),
				except that you do not pass arrays to the function but keys (string!) of the columns not
				TBD

		Example:
			(begin example)
			sortDataSet(data set, column1[, mixed arg [, mixed ... [, array ...]]])
			(end example)

		See Also:
			- array_multisort()
	*/
	function sortDataSet(&$dataSet) {
		$args = func_get_args();
		$callString = 'array_multisort(';
		$usedColumns = array();
		for ($i = 1, $count = count($args); $i < $count; ++$i) {
			switch(gettype($args[$i])) {
				case 'string':
					$callString .= '$dataSet[\'' . $args[$i] . '\'], ';
					array_push($usedColumns, $args[$i]);
					break;
				case 'integer':
					$callString .= $args[$i] . ', ';
					break;
				default:
					throw new Exception('expected string or integer, given ' . gettype($args[$i]));
			}
		}
		foreach($dataSet as $column => $array) {
			if(in_array($column, $usedColumns)) continue;
			$callString .= '$dataSet[\'' . $column . '\'], ';
		}
		eval(substr($callString, 0, -2) . ');');
	}

	/*
		Function: makeTagString
			TBD

		Arguments:
			tags - TBD

		Returns:
			TBD
	*/
	static function makeTagString($tags)
	{
		$tags_string = "";
		$firstpass = true;
		if (is_array($tags))
		{
			foreach ($tags as $tag)
			{
				if (trim($tag) != "" and !is_null($tag))
				{
					$tag = str_replace("-", " ", $tag);
					if ($firstpass)
					{
						$tags_string = trim($tag);
						$firstpass = false;
					}
					else
					{
						$tags_string .= ', ' . strtolower(trim($tag));
					}
				}
			}
		}
		return $tags_string;
	}

	/*
		Function: makeTagArray
			TBD

		Arguments:
			tag_string - TBD

		Returns:
			TBD
	*/
	static function makeTagArray($tag_string)
	{
		$tags = explode (',', $tag_string);
		$tag_filter = new RivetyCore_FilterTags();
		$tag_array = array();
		foreach ($tags as $tag)
		{
			if (trim($tag) != "" and $tag != "none" and !is_null($tag))
			{
				$tag = $tag_filter->filter($tag);
				if (!in_array($tag, $tag_array)) $tag_array[] = $tag;
			}
		}
		return $tag_array;
	}

	/*
		Function: xyago
			TBD

		Arguments:
			datefrom - TBD
			dateto - TBD

		Returns:
			TBD
	*/
	static function xyago($datefrom, $dateto = -1)
	{
		// Defaults and assume if 0 is passed in that
		// its an error rather than the epoch

		if ($datefrom == 0) return "A long time ago";
		if ($dateto == -1) $dateto = time();

		// Calculate the difference in seconds betweeen
		// the two timestamps

		$difference = $dateto - $datefrom;

		// If difference is less than 60 seconds,
		// seconds is a good interval of choice

		if ($difference < 60)
		{
			$interval = "s";
		}

		// If difference is between 60 seconds and
		// 60 minutes, minutes is a good interval
		elseif($difference >= 60 && $difference < 60 * 60)
		{
			$interval = "n";
		}

		// If difference is between 1 hour and 24 hours
		// hours is a good interval
		elseif($difference >= 60 * 60 && $difference < 60 * 60 * 24)
		{
			$interval = "h";
		}

		// If difference is between 1 day and 7 days
		// days is a good interval
		elseif($difference >= 60 * 60 * 24 && $difference < 60 * 60 * 24 * 7)
		{
			$interval = "d";
		}

		// If difference is between 1 week and 30 days
		// weeks is a good interval
		elseif ($difference >= 60 * 60 * 24 * 7 && $difference < 60 * 60 * 24 * 30)
		{
			$interval = "ww";
		}

		// If difference is between 30 days and 365 days
		// months is a good interval, again, the same thing
		// applies, if the 29th February happens to exist
		// between your 2 dates, the function will return
		// the 'incorrect' value for a day
		elseif ($difference >= 60 * 60 * 24 * 30 && $difference < 60 * 60 * 24 * 365) {
			$interval = "m";
		}

		// If difference is greater than or equal to 365
		// days, return year. This will be incorrect if
		// for example, you call the function on the 28th April
		// 2008 passing in 29th April 2007. It will return
		// 1 year ago when in actual fact (yawn!) not quite
		// a year has gone by
		elseif ($difference >= 60 * 60 * 24 * 365) {
			$interval = "y";
		}

		// Based on the interval, determine the
		// number of units between the two dates
		// From this point on, you would be hard
		// pushed telling the difference between
		// this function and DateDiff. If the $datediff
		// returned is 1, be sure to return the singular
		// of the unit, e.g. 'day' rather 'days'

		switch ($interval) {
			case "m":
				$months_difference = floor($difference / 60 / 60 / 24 / 29);
				while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
					$months_difference++;
				}
				$datediff = $months_difference;

				// We need this in here because it is possible
				// to have an 'm' interval and a months
				// difference of 12 because we are using 29 days
				// in a month

				if ($datediff == 12) {
					$datediff--;
				}

				$res = ($datediff == 1) ? "$datediff month ago" : "$datediff months ago";
				break;

			case "y":
				$datediff = floor($difference / 60 / 60 / 24 / 365);
				$res = ($datediff==1) ? "$datediff year ago" : "$datediff years ago";
				break;

			case "d":
				$datediff = floor($difference / 60 / 60 / 24);
				$res = ($datediff==1) ? "$datediff day ago" : "$datediff days ago";
				break;

			case "ww":
				$datediff = floor($difference / 60 / 60 / 24 / 7);
				$res = ($datediff==1) ? "$datediff week ago" : "$datediff weeks ago";
				break;

			case "h":
				$datediff = floor($difference / 60 / 60);
				$res = ($datediff==1) ? "$datediff hour ago" : "$datediff hours ago";
				break;

			case "n":
				$datediff = floor($difference / 60);
				$res = ($datediff==1) ? "$datediff minute ago" : "$datediff minutes ago";
				break;

			case "s":
				$datediff = $difference;
				$res = ($datediff==1) ? "$datediff second ago" : "$datediff seconds ago";
				break;
		}
		return $res;
	}

	/*
		Function: convertlinebreaks
			TBD

		Arguments:
			text - The string to convert.

		Returns
			TBD
	*/
	static function convertlinebreaks($text) {
		return preg_replace("/\015\012|\015|\012/", "\n", trim($text));
	}

	// /*
	//	Function: quoteInto
	//		TBD
	//
	//	Arguments:
	//		text - TBD
	//		params - TBD
	//
	//	Returns:
	//		TBD
	// */
	// static function quoteInto($text, $params) {
	//	if (!is_array($params)) {
	//		$params = array($params);
	//	}
	// }

	// /*
	// 	Function: pluralOrSingular
	// 		Takes a quantity and two noun forms and returns the noun form that suits the quantity.
	// 		Useful when a dynamic quantity is used in a sentence, such as, "There are 2 users online."
	// 
	// 	Arguments:
	// 		qty - The quantity to compare.
	// 		singular - The singular noun form.
	// 		plural - The plural noun form.
	// 
	// 	Returns:
	// 		The singular or plural noun form, depending on the quantity given.
	// */
	// static function pluralOrSingular($qty, $singular, $plural)
	// {
	// 	return ($qty == 1) ? $singular : $plural;
	// }

	// /*
	// 	Function: getGenderArray
	// 		Gets a list of possible gender choices.
	// 
	// 	Returns:
	// 		An array containing three gender choices: male, female, and unspecified.
	// */
	// static function getGenderArray() {
	// 	return array('male' => 'male', 'female' => 'female', 'unspecified' => 'unspecified');
	// }

	// /*
	// 	Function: getCaProvincesArray
	// 		Gets a list of Canadian Provinces (cloned from getUsStatesArray).
	// 
	// 	Arguments:
	// 		choose_one (optional) - A string to place at the beginning of the returned array.
	// 
	// 	Returns:
	// 		An array of provinces.
	// */
	// static function getCaProvincesArray($choose_one = null) {
	// 	$states = array(
	// 					'AB'=>'Alberta',
	// 					'BC'=> 'British Columbia',
	// 					'MB'=>'Manitoba',
	// 					'NB'=>'New Brunswick',
	// 					'NL'=>'Newfoundland',
	// 					'NT'=>'Northwest Territories',
	// 					'NS'=>'Nova Scotia',
	// 					'NU'=>'Nunavut',
	// 					'ON'=>'Ontario',
	// 					'PE'=>'Prince Edward Island',
	// 					'QC'=>'Quebec',
	// 					'SK'=>'Saskatchewan',
	// 					'YT'=>'Yukon'
	// 					);
	// 	if (!is_null($choose_one)) {
	// 		$choose_one_ar = array(null => $choose_one);
	// 		$states = array_merge($choose_one_ar, $states);
	// 	}
	// 	return $states;
	// }

	// /*
	// 	Function: getUsStatesArray
	// 		Gets a list of states and territories of the United States.
	// 
	// 	Arguments:
	// 		choose_one (optional) - A string to place at the beginning of the returned array.
	// 
	// 	Returns:
	// 		An array of states.
	// */
	// static function getUsStatesArray($choose_one = null) {
	// 	$states = array(
	// 	  'AL' => 'Alabama',
	// 	  'AK' => 'Alaska',
	// 	  'AZ' => 'Arizona',
	// 	  'AR' => 'Arkansas',
	// 	  'CA' => 'California',
	// 	  'CO' => 'Colorado',
	// 	  'CT' => 'Connecticut',
	// 	  'DE' => 'Delaware',
	// 	  'DC' => 'District of Columbia',
	// 	  'FL' => 'Florida',
	// 	  'GA' => 'Georgia',
	// 	  'HI' => 'Hawaii',
	// 	  'ID' => 'Idaho',
	// 	  'IL' => 'Illinois',
	// 	  'IN' => 'Indiana',
	// 	  'IA' => 'Iowa',
	// 	  'KS' => 'Kansas',
	// 	  'KY' => 'Kentucky',
	// 	  'LA' => 'Louisiana',
	// 	  'ME' => 'Maine',
	// 	  'MD' => 'Maryland',
	// 	  'MA' => 'Massachusetts',
	// 	  'MI' => 'Michigan',
	// 	  'MN' => 'Minnesota',
	// 	  'MS' => 'Mississippi',
	// 	  'MO' => 'Missouri',
	// 	  'MT' => 'Montana',
	// 	  'NE' => 'Nebraska',
	// 	  'NV' => 'Nevada',
	// 	  'NH' => 'New Hampshire',
	// 	  'NJ' => 'New Jersey',
	// 	  'NM' => 'New Mexico',
	// 	  'NY' => 'New York',
	// 	  'NC' => 'North Carolina',
	// 	  'ND' => 'North Dakota',
	// 	  'OH' => 'Ohio',
	// 	  'OK' => 'Oklahoma',
	// 	  'OR' => 'Oregon',
	// 	  'PA' => 'Pennsylvania',
	// 	  'PR' => 'Puerto Rico',
	// 	  'RI' => 'Rhode Island',
	// 	  'SC' => 'South Carolina',
	// 	  'SD' => 'South Dakota',
	// 	  'TN' => 'Tennessee',
	// 	  'TX' => 'Texas',
	// 	  'UT' => 'Utah',
	// 	  'VT' => 'Vermont',
	// 	  'VI' => 'Virgin Island',
	// 	  'VA' => 'Virginia',
	// 	  'WA' => 'Washington',
	// 	  'WV' => 'West Virginia',
	// 	  'WI' => 'Wisconsin',
	// 	  'WY' => 'Wyoming'
	// 	);
	// 	if (!is_null($choose_one)) {
	// 		$choose_one_ar = array(null => $choose_one);
	// 		$states = array_merge($choose_one_ar, $states);
	// 	}
	// 	return $states;
	// }

	// /*
	// 	Function: getUsStatesArray
	// 		Gets a list of states and territories of the United States.
	// 
	// 	Arguments:
	// 		choose_one (optional) - A string to place at the beginning of the returned array.
	// 
	// 	Returns:
	// 		An array of states.
	// */
	// static function getCanadianProvincesArray($choose_one = null) {
	// 	$provinces = array(
	// 	  'AB' => 'Alberta',
	// 	  'BC' => 'British Columbia',
	// 	  'MB' => 'Manitoba',
	// 	  'NB' => 'New Brunswick',
	// 	  'NL' => 'Newfoundland and Labrador',
	// 	  'NS' => 'Nova Scotia',
	// 	  'ON' => 'Ontario',
	// 	  'PE' => 'Prince Edward Island',
	// 	  'QC' => 'Quebec',
	// 	  'SK' => 'Saskatchewan'
	// 	);
	// 	if (!is_null($choose_one)) {
	// 		$choose_one_ar = array(null => $choose_one);
	// 		$provinces = array_merge($choose_one_ar, $provinces);
	// 	}
	// 	return $provinces;
	// }

	// /*
	// 	Function: get_timezone_offset
	// 		Calculates the offset from the origin timezone to the remote timezone in seconds.
	// 
	// 	Arguments:
	// 		remote_tz - The remote time zone.
	// 		origin_tz (optional) - The origin time zone. Defaults to the server's time zone.
	// 
	// 	Returns:
	// 		The number of seconds difference between the given time zones (integer).
	// */
	// static function get_timezone_offset($remote_tz, $origin_tz = null) {
	// 	if($origin_tz === null) {
	// 		if(!is_string($origin_tz = date_default_timezone_get())) {
	// 			return false; // A UTC timestamp was returned -- bail out!
	// 		}
	// 	}
	// 	$origin_dtz = new DateTimeZone($origin_tz);
	// 	$remote_dtz = new DateTimeZone($remote_tz);
	// 	$origin_dt = new DateTime("now", $origin_dtz);
	// 	$remote_dt = new DateTime("now", $remote_dtz);
	// 	$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	// 	return $offset;
	// }

	/*
		Function: dateDiffInDays
			Calculates the number of days between two dates.

		Arguments:
			pastdate - The earlier of the dates to compare.
			futuredate - The later of the dates to compare.

		Returns:
			The number of days between the two dates (integer).
	*/
	static function dateDiffInDays($pastdate, $futuredate) {
		return floor((strtotime($futuredate) - $pastdate) / (24 * 60 * 60));
	}

	/*
		Function: makeSeoFriendly
			Makes a string suitable for use as an SEO-friendly URL.
			Lowercases the entire string and replaces spaces with hyphens (dashes).

		Arguments:
			value - The string to reformat.

		Returns:
			A reformatted version of the input string.
	*/
	static function makeSeoFriendly($value)
	{
		$filterChain = new Zend_Filter();
		$filterChain->addFilter(new Zend_Filter_Alnum(true))->addFilter(new Zend_Filter_StringToLower());
		return str_replace(' ', '-', $filterChain->filter($value));
	}

	// /*
	// 	Function: makeDummyText
	// 		Generates latin text to the desired character length.
	// 
	// 	Arguments:
	// 		chars - The maximum number of chars to return. Default is 150.
	// 
	// 	Returns:
	// 		A string containing the requested amount of latin copy.
	// */
	// static function makeDummyText($chars) {
	// 	if (is_null($chars)) {
	// 		$chars = 150;
	// 	}
	// 
	// 	$text = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vivamus libero leo, " .
	// 			"malesuada non, gravida ac, lobortis scelerisque, magna. Cras condimentum metus " .
	// 			"id sapien. Donec erat velit, fringilla vitae, varius id, laoreet at, urna. Donec " .
	// 			"quam velit, gravida vitae, bibendum sed, suscipit vel, sem. Vivamus interdum orci " .
	// 			"vel odio. Vestibulum nibh turpis, luctus eget, rutrum nec, luctus vel, lorem. " .
	// 			"Quisque vulputate, velit vitae tincidunt malesuada, nibh tortor placerat ipsum, " .
	// 			"sed lobortis lectus mi sit amet pede.\n Aenean sodales. Vestibulum ante ipsum primis " .
	// 			"in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean mattis neque sit " .
	// 			"amet mauris. Curabitur adipiscing dictum risus. Donec imperdiet odio eget mi mattis " .
	// 			"suscipit. Nulla ac libero ut sapien ultrices laoreet. Nam feugiat condimentum odio. " .
	// 			"Fusce luctus justo id velit. Nunc eget tellus quis libero scelerisque tincidunt. " .
	// 			"Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; " .
	// 			"Aliquam suscipit urna at velit. Duis euismod. Nunc vitae metus.";
	// 
	// 	$text_length = strlen($text);
	// 	if ($chars > $text_length) {
	// 		$repeat = ceil($chars / $text_length);
	// 		for($x = 0; $x < $repeat; $x++){
	// 			$text .= $text;
	// 		}
	// 	}
	// 
	// 	return substr($text, 0, $chars);
	// }

	// /*
	// 	Function: has
	// 		Check to see if an object/array contains a certain key.
	// 
	// 	Arguments:
	// 		obj - The object or array to search in (haystack).
	// 		key - The key to try to find in the object (needle).
	// 
	// 	Returns:
	// 		A boolean indicating whether the key does exist (true) or does not (false).
	// */
	// static function has($obj, $key) {
	// 	if (array_key_exists($key, get_object_vars($obj))) {
	// 		return true;
	// 	} else {
	// 		return false;
	// 	}
	// }

	/*
		Function: getViewState

		Arguments:
			session - The entire current session.
			key - The key to look for in the view state array.
			default - The view state to use if nothing turns up.

		Returns:
			The view state for the given key, or the default if the key does not exist.
	*/
	static function getViewState($session, $key, $default) {
		// TODO - Rich fix this
		// if (array_key_exists($key, $session->view_states)) {
		//	if ($session->view_states[$key] != '') {
		//		return($session->view_states[$key]);
		//	} else {
		//		return($default);
		//	}
		// } else {
		return($default);
		// }
	}

	// /*
	// 	Function: parseCsv
	// */
	// static function parseCsv($filename, $delimiter = "|", $first_row_is_fields = true) {
	// 	$row = 1;
	// 	$handle = fopen($filename, "r");
	// 	$output = array();
	// 	$fields = array();
	// 	while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
	// 		if ($row == 1 and $first_row_is_fields) {
	// 			foreach ($data as $field) {
	// 				$fields[] = $field;
	// 			}
	// 		} else {
	// 			$tmp_out = array();
	// 			if (count($fields) > 0) {
	// 				for ($x = 0; $x < count($data); $x++) {
	// 					$tmp_out[$fields[$x]] = $data[$x];
	// 				}
	// 			} else {
	// 				foreach ($data as $col) {
	// 					$tmp_out[] = $col;
	// 				}
	// 			}
	// 			$output[] = $tmp_out;
	// 		}
	// 		$row++;
	// 	}
	// 	fclose($handle);
	// 	return $output;
	// }

	/*
		Function: vnsprintf
			Acts just like PHP's built-in vsprintf except it can use named keys.
	*/
	static function vnsprintf($format, array $data) {
		preg_match_all('/ (?<!%) % ( (?: [[:alpha:]_-][[:alnum:]_-]* | ([-+])? [0-9]+ (?(2) (?:\.[0-9]+)? | \.[0-9]+ ) ) ) \$ [-+]? \'? .? -? [0-9]* (\.[0-9]+)? \w/x', $format, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		$offset = 0;
		$keys = array_keys($data);
		foreach($match as &$value) {
			if (( $key = array_search( $value[1][0], $keys, TRUE)) !== FALSE || (is_numeric($value[1][0]) && ($key = array_search((int)$value[1][0], $keys, TRUE)) !== FALSE)) {
				$len = strlen($value[1][0]);
				$format = substr_replace($format, 1 + $key, $offset + $value[1][1], $len);
				$offset -= $len - strlen(1 + $key);
			}
		}
		return vsprintf($format, $data);
	}

	/*
		Function: XMLEntities
	*/
	static function XMLEntities($string)
	{
		// copied from php manual comments
		$string = preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', RivetyCore_Common::_privateXMLEntities("$0"), $string);
		return $string;
	}

	/*
		Function: _privateXMLEntities
	*/
	private static function _privateXMLEntities($num)
	{
		// TODO - is it weird that this is a private static method ?
		// copied from php manual comments
		$chars = array(
			128 => '&#8364;',
			130 => '&#8218;',
			131 => '&#402;',
			132 => '&#8222;',
			133 => '&#8230;',
			134 => '&#8224;',
			135 => '&#8225;',
			136 => '&#710;',
			137 => '&#8240;',
			138 => '&#352;',
			139 => '&#8249;',
			140 => '&#338;',
			142 => '&#381;',
			145 => '&#8216;',
			146 => '&#8217;',
			147 => '&#8220;',
			148 => '&#8221;',
			149 => '&#8226;',
			150 => '&#8211;',
			151 => '&#8212;',
			152 => '&#732;',
			153 => '&#8482;',
			154 => '&#353;',
			155 => '&#8250;',
			156 => '&#339;',
			158 => '&#382;',
			159 => '&#376;',
		);
		$num = ord($num);
		return (($num > 127 && $num < 160) ? $chars[$num] : "&#" . $num . ";" );
	}

	/*
		Function: truncateString
	*/
	public static function truncateString($intLength = 0, $strText = "")
	{
		if ($intLength == 0)
		{
			return $strText;
		}
		if (strlen($strText) > $intLength)
		{
			// preg_match("/[a-zA-Z0-9]{0, " . $intLength . "}/", $strText, $strNewText); // this doesn't seem to work
			$strNewText = substr($strText, 0, $intLength);
			return $strNewText . "…";
		}
		else
		{
			return $strText;
		}
	}

	/*
		Function: generatePassword
	*/
	public static function generatePassword($strength = 0, $length = 8)
	{
		$password = "";
		switch ($strength)
		{
			case 1:
				$possible = "0123456789bcdfghjklmnpqrstvwxyz";
				break;
			case 2:
				$possible = "!@#$%^&0123456789bcdfghjklmnpqrstvwxyz";
				break;
			default:
				$possible = "bcdfghjklmnpqrstvwxyz";
				break;
		}
		$i = 0;
		while ($i < $length)
		{
			$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
			if (!strstr($password, $char))
			{
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}

	public static function replaceWithArray($string, $replacements)
	{
		foreach($replacements as $key => $val)
		{
			$string = str_replace($key, $val, $string);
		}
		return $string;
	}

}
