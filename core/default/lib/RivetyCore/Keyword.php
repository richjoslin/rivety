<?php

/*
	Class: RivetyCore_Keyword

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>
*/
class RivetyCore_Keyword {

	/* Group: Instance Methods */

	/*
		Function: extract

		Arguments:
			source - TBD

		Returns:
			An array of keywords.

		See Also:
			Yahoo's Term Extraction Service <http://developer.yahoo.com/search/content/V1/termExtraction.html>
	*/
	function extract($source) {
		// TODO - should remove this default value
		$rivety_id = RivetyCore_Registry::get('yahoo_api_rivety_id');
		$curl_handle = curl_init();
		$keywords = null;
		$all_keywords = null;
		$filter = new RivetyCore_FilterTags();
		$noisewords = RivetyCore_NoiseWords::getAll();
		$url = "http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction";
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_POST, 1);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "appid=".$rivety_id."&output=php&context=".urlencode($source));
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		$results= unserialize($buffer);
		if (is_array($results['ResultSet'])) {
			if (!is_array($results['ResultSet']['Result'])) {
				$all_keywords = array($results['ResultSet']['Result']);
			} else {
				$all_keywords = $results['ResultSet']['Result'];
			}
		}
		$keywords = array();
		if (is_array($all_keywords)) {
			foreach ($all_keywords as $keyword) {
				// this is probably overkill, but in case I ever need to check for other things, I'm okay.
				$errors = 0;
				if (in_array($keyword, $noisewords)) {
					$errors++;
				}
				if ($errors == 0) {
					$keywords[] = $filter->filter($keyword);
				}
			}
		}
		return $keywords;
	}

}
