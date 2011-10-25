<?php

/*
	Class: Countries

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
	 	<RivetyCore_Db_Table_Abstract>
*/
class Countries extends RivetyCore_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
    protected $_name = 'default_countries';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
    protected $_primary = 'country_code';

	/* Group: Instance Methods */

	public function getAll() {
		$tmp_countries = $this->fetchAll(null, array('sortorder','country'));
		if (!is_null($tmp_countries)) {
			return $tmp_countries->toArray();
		} else {
			return array();
		}
	}

	/*
		Function: getCountriesArray
			DEPRECATED.
			Succeeded by <getOptions> which does not require a
			null option to be inserted at the top of the array.
			Gets an array of countries from the database.

		Arguments:
			nullval (optional) - A string to describe the first entry in the returned array which will have a null value. Default is "Any".

		Returns:
			An array of key-value pairs representing countries.
	*/
    public function getCountriesArray($nullval = "Any") {
    	$countries = array();
    	$countries[null] = $nullval;
    	$db_countries = $this->fetchAll(null, array('sortorder','country'));
    	foreach ($db_countries as $country) {
    		$countries[$country->country_code] = $country->country;
    	}
    	return $countries;
    }

	/*
		Function: getOptions
			Gets an array of countries from the database.
			The array returned fits perfectly with Smarty's functions html_options, html_checkboxes, etc.

		Arguments:
			first_option (optional) - A string to describe the first entry in the returned array which will have a null value.

		Returns: array of country options
	*/
    public function getOptions($first_option = null) {
    	$options = array();
		if (!is_null($first_option)) {
	    	$options[''] = $first_option;
		}
    	$tmp_countries = $this->fetchAll(null, array('sortorder','country'));
    	foreach ($tmp_countries as $country) {
    		$options[$country->country_code] = $country->country;
    	}
    	return $options;
    }
	
	/*
		Function: getCountryRegions
			Gets an array of countries and their regions (continents) from the database.

		Returns: array of countries and their regions (continents)
	*/
    public function getCountryRegions() {
    	$options = array();
    	$tmp_countries = $this->fetchAll(null, array('sortorder','country'));
    	foreach ($tmp_countries as $country) {
    		$options[$country->country] = $country->continent;
    	}
    	return $options;
    }

	/*
		Function: getRegionsArray
			Gets an array of regions from the database. Typically continents.

		Arguments:
			nullval (optional) - A string to describe the first entry in the returned array which will have a null value. Default is "Any".

		Returns:
			An array of key-value pairs representing regions.
	*/
    public function getRegionsArray($nullval = "Any") {
    	$regions = array();
    	$regions[null] = $nullval;
    	$regions['AF'] = "Africa";
    	$regions['AS'] = "Australia";
    	$regions['EU'] = "Europe";    	
    	$regions['NA'] = "North America";
    	$regions['SA'] = "South America";
    	$regions['OC'] = "Oceania";
    	return $regions;
    }
}
