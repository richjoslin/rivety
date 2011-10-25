<?php

/*
	Class: Backup

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Controller_Action_Abstract>
		<RivetyCore_Controller_Action_Admin>
*/
class BackupController extends  RivetyCore_Controller_Action_Admin {

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			For this class, does nothing other than initialize the parent object (calls init() on the parent instance).
	*/
	function init() {
        parent::init();
    }

	/* Group: Actions */

	/*
		Function: index
			Allows backup/export of tables

	*/
    function indexAction() {
    	/*
    	$table = new Users();
    	header('Content-Type: text/xml');
    	die($table->dumpData());
    	*/
    }
        
}
