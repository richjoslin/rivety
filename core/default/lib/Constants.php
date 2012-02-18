<?php

class Constants
{

	function Constants()
	{
		define('_DS',DIRECTORY_SEPARATOR);
		define('_PS',PATH_SEPARATOR);
		define('DB_DATE_FORMAT',"Y-m-d");
		define('DB_DATETIME_FORMAT',"Y-m-d H:i:s");
		define('URL_REGEX','@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@');
		define('URL_REGEX_REPLACE','<a href="$1">$1</a>');
	}

}
