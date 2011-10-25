<?php

/*
	Class: CacheTags

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>

	About: See Also
	 	<RivetyCore_Db_Table_Abstract>
*/
class CacheTags extends RivetyCore_Db_Table_Abstract {

	/* Group: Instance Variables */

	/*
		Variable: $_name
			The name of the table or view to interact with in the data source.
	*/
    protected $_name = 'default_cache_tags';

	/*
		Variable: $_primary
			The primary key of the table or view to interact with in the data source.
	*/
    protected $_primary = 'id';

}
