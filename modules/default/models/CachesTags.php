<?php

/*
	Class: CachesTags

	About: Author
		Rich Joslin

	About: License
		<http://communit.as/docs/license>

	About: See Also
	 	<Cts_Db_Table_Abstract>
*/
class CachesTags extends Cts_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
    protected $_name = 'default_caches_tags';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
    protected $_primary = array('cache_id', 'tag_id');

}
