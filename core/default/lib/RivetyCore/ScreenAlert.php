<?php

/*
	Class: RivetyCore_ScreenAlert

	About: Author
		Rich Joslin

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_ScreenAlert
{

	/*
		Function: Constructor

		Arguments:
			username - sdfsdf.
			type - sdfsdf.
			message - A string that is to be displayed on the screen.
			expires - A unix timestamp representing when this message expires.
			mca - The module-controller-action that this alert should appear in. Ex.: default_roleadmin_index
	*/
	function __construct($username, $type, $message, $expires = null, $mca = null)
	{
		$this->username = $username;
		$this->type = $type;
		$this->message = $message;
		$this->expires = $expires;
		$this->mca = $mca;
	}

	function queue()
	{
		$alerts_dbtable = new ScreenAlerts();
		$alerts_dbtable->insert(array(
			'username' => $this->username,
			'type' => $this->type,
			'message' => $this->message,
			'mca' => $this->mca,
			'created' => date(DB_DATETIME_FORMAT),
			'expires' => date(DB_DATETIME_FORMAT, $this->expires),
		));
	}

}
