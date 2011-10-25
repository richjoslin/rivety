<?php

/*
	Class: Locales

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Db_Table_Abstract>
*/
class Locales extends RivetyCore_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
    protected $_name = 'default_locales';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
    protected $_primary = array('country_code', 'locale_code');   

	/* Group: Instance Methods */

	function getDistinctRegions() {
		return $this->fetchAllArray(
			$this->select()
			->from('default_locales', array('region_name'))
			->distinct()
		);
	}

	function getDistinctCountryCodes() {
		return $this->fetchAllArray(
			$this->select()
			->from('default_locales', array('country_code', 'country_name'))
			->distinct()
		);
	}

	function getDistinctCountries($region_name) {
		return $this->fetchAllArray(
			$this->select()
			->from('default_locales', array('country_code', 'country_name'))
			->where("region_name = ?", $region_name)
			->distinct()
		);
	}
	

	function getDistinctPseudoCountryCodes($region_name) {
		return $this->fetchAllArray(
			$this->select()
			->from('default_locales', array('pseudo_country_code'))
			->where("region_name = ?", $region_name)
			->where("pseudo_country_code IS NOT NULL")
			->distinct()
		);
	}

	function getLanguages($region_name, $country_code) {
		return $this->fetchAllArray(
			$this->select()
			->from('default_locales', array('language_code', 'language_name', 'pseudo_country_code', 'pseudo_language_code'))
			->order('language_name asc')
			->where("region_name = ?", $region_name)
			->where("country_code = ?", $country_code)
		);
	}

	function getLocaleCodes($lowercase = false) {
		$tmp_locales = $this->fetchAllArray(
			$this->select()
			->from('default_locales', array('language_code', 'country_code'))
			->distinct()
		);
		$locale_codes = array();
		foreach ($tmp_locales as $tmp_locale) {
			if ($lowercase) {
				$locale_codes[] = $tmp_locale['language_code']."-".strtolower($tmp_locale['country_code']);
			} else {
				$locale_codes[] = $tmp_locale['language_code']."-".$tmp_locale['country_code'];
			}
		}
		return $locale_codes;
	}
	
	function getLocaleCodesArray($lowercase, $exclude_pseudo=false){
		$query = $this->select()
			->from('default_locales', array('language_code', 'country_code','country_name','language_name'))
			->distinct();
			
		if ($exclude_pseudo) {
			$query = $query->where('pseudo_country_code IS NULL');
		}
			
		$tmp_locales = $this->fetchAllArray($query);
		
		$locale_codes = array();
		foreach ($tmp_locales as $tmp_locale) {
			if ($lowercase) {
				$key = $tmp_locale['language_code']."-".strtolower($tmp_locale['country_code']);
			} else {
				$key = $tmp_locale['language_code']."-".$tmp_locale['country_code'];
			}
			$locale_codes[$key] = $tmp_locale['country_name'] . " - " . $tmp_locale['language_name']; 
			
		}
		return $locale_codes;
		
	}

	function fetchByLocaleCode($locale_code){	
		$pos = strpos($locale_code,"-");
		$country_code = strtoupper(substr($locale_code, $pos+1));
		$language_code = strtolower(substr($locale_code,0,$pos));
		return $this->fetchAllArray($this->select()->where("country_code = ?",$country_code)->where('language_code = ?',$language_code));		
	}

}
