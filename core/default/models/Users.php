<?php

/*
	Class: Users

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<RivetyCore_Db_Table_Abstract>
*/
class Users extends RivetyCore_Db_Table_Abstract
{

	protected $_name = 'default_users';
	protected $_primary = 'username';
	protected $_logger;
	// public $_keyword_search_field_names = array("username", "full_name");
	public $_keyword_search_field_names = array("username", "email");

	/* Group: Instance Methods */

	/*
		Function: insert
			Inserts a user into the database.

		Arguments:
			data - An array of attributes associated with a user.

		Returns:
			TBD
	*/
	public function insert(array $data)
	{
		// md5 password if it's not blank
		if (!empty($data['password'])) $data['password'] = md5($data['password']);
		if (!empty($data['password_hash']))
		{
			$data['password'] = $data['password_hash'];
			unset($data['password_hash']);
		}
		$timestamp = date("Y-m-d H:i:s") ;
		$data['created_on'] = $timestamp;
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		return parent::insert($data);
	}

	/*
		Function: isEmailInUse
			Determines whether a particular email address already exists in the database.

		Arguments:
			email - The email address string to look for.
			username (optional) - If specified, the username gets added to the where clause so you can see if an email address is being used with a particular username.

		Returns:
			A boolean indicating whether the email exists (true) or does not (false).
	*/
	public function isEmailInUse($email, $username = null)
	{
		$email_where = $this->getAdapter()->quoteInto('email = ?', $email);
		if (!is_null($username)) $email_where .= $this->getAdapter()->quoteInto(' and username <> ?', $username);
		return ($this->getCountByWhereClause($email_where) > 0);
	}

	/*
		Function: fetchByUsername
			Gets a set of user data for a particular username from the database.

		Arguments:
			username - The username for the user to be retrieved.

		Returns:
			TBD
	*/
	public function fetchByUsername($username)
	{
		return $this->fetchRow($this->getAdapter()->quoteInto('username = ?', $username));
	}

	// /*
	//	Function: getFullNameByUsername
	//		Gets a full name string for a particular username from the database.
	//
	//	Arguments:
	//		username - The username for the user to be retrieved.
	//
	//	Returns: string
	// */
	// public function getFullNameByUsername($username) {
	//	$user = $this->findArray($username);
	//	if (count($user) == 0) {
	//		return null;
	//	} else {
	//		return $user['full_name'];
	//	}
	// }

	/*
		Function: delete
			Delete a user from the database.

		Arguments:
			where - A where clause string to limit the rows that get deleted. If the where clause is empty, all users will be deleted!

		Plugin Hooks:
			- *default_users_table_delete* (action) - TBD
			param username - The username of the user to delete.

		Returns:
			TBD
	*/
	public function delete($where)
	{
		$users = $this->fetchAll($where);
		foreach ($users as $user)
		{
			$this->_rivety_plugin->doAction('default_users_table_delete', array('username' => $user->username)); // ACTION HOOK
		}
		return parent::delete($where);
	}

	/*
		Function: update
			Updates the attributes of a user in the database.

		Arguments:
			data - An array with a key-value pair for each column in the users table.
			where - A where clause string to limit the rows that get updated. If the where clause is empty, all users will be updated with the data argument.

		Returns:
			TBD
	*/
	public function update(array $data, $where)
	{
		// md5 password if it's not blank
		if (!empty($data['password'])) $data['password'] = md5($data['password']);
		if (!empty($data['password_hash']))
		{
			$data['password'] = $data['password_hash'];
			unset($data['password_hash']);
		}
		return parent::update($data, $where);
	}

	/*
		Function: userExists
			Used to determine if a user exists.

		Arguments:
			username - The username to check for.

		Returns:
			A boolean telling whether the user exists (true) or does not (false).
	*/
	public function userExists($username)
	{
		$where = $this->getAdapter()->quoteInto('username = ?', $username);
		$user = $this->fetchRow($where);
		return (!is_null($user));
	}

	/*
		Function: getByRoleId
			Gets all users in a particular role.

		Arguments:
			role_id - The ID of the role from which to retrieve all users.

		Returns:
			An array of users.
	*/
	function getByRoleId($role_id)
	{
		// SELECT u.* from default_users u join default_users_roles r on u.username = r.username where r.role_id = 3;
		$where = $this->getAdapter()->quoteInto('role_id = ?', $role_id);
		$select = $this->getAdapter()->select();
		$select->from(array("u" => $this->_name));
		$select->join(array("r" => "default_users_roles"),"u.username = r.username",array());
		$select->where("r.role_id = ?",$role_id);
		$db = $this->getAdapter();
		$out = $db->fetchAll($select);
		return $out;
	}

	// /*
	//	Function: setMetaData
	//		Updates the metadata object for a user.
	//
	//	Arguments:
	//		username - username that will have thier metadata updated
	//		key - the field name of the metadata field
	//		value - the metadata value to be set
	// */
	// function setMetaData($username, $key, $value) {
	//	$user = $this->fetchByUsername($username);
	//	if (!is_null($user)) {
	//		if (!is_null($user->metadata)) {
	//			$metadata = unserialize($user->metadata);
	//		} else {
	//			$metadata = array();
	//		}
	//		$metadata[$key] = $value;
	//		$user->metadata = serialize($metadata);
	//		$user->save();
	//	}
	// }

	// /*
	//	Function: getMetaData
	//		Updates the metadata object for a user.
	//
	//	Arguments:
	//		username - username that will have thier metadata updated
	//		key (optional)- the field name of the metadata field. If this isn't specifed, an associative array with all values is returned.
	// */
	// function getMetaData($username, $key = null) {
	//	$user = $this->fetchByUsername($username);
	//	$out = null;
	//	if (!is_null($user)) {
	//		if (!is_null($user->metadata)) {
	//			$metadata = unserialize($user->metadata);
	//			if (is_null($key)) {
	//				$out = $metadata;
	//			} else {
	//				if (array_key_exists($key,$metadata)) {
	//					$out = $metadata[$key];
	//				}
	//			}
	//		}
	//	}
	//	return $out;
	// }

	/*
		Function: getRecentlyRegistered
			Fetches a list of those users who have most recently registered.

		Arguments:
			how_many - An integer value which is passed as the limit parameter in the database query.

		Returns: array of users or an empty array
	*/
	function getRecentlyRegistered($how_many)
	{
		$tmp_users = $this->fetchAll($this->select()->order("created_on desc")->limit($how_many));
		if (!is_null($tmp_users)) return $tmp_users->toArray();
		else return array();
	}

	/*
		Function: getRecentlyUpdated
			Fetches a list of those users who have most recently updated.

		Arguments:
			how_many - An integer value which is passed as the limit parameter in the database query.

		Returns: array of users or an empty array
	*/
	function getRecentlyUpdated($how_many)
	{
		$tmp_users = $this->fetchAll($this->select()->order("last_modified_on desc")->limit($how_many));
		if (!is_null($tmp_users)) return $tmp_users->toArray();
		else return array();
	}

	/*
		Function: getRecentlyOnline
			Fetches a list of those users who have most recently logged into the app.

		Arguments:
			how_many - An integer value which is passed as the limit parameter in the database query.

		Returns: array of users or an empty array
	*/
	function getRecentlyOnline($how_many)
	{
		$tmp_users = $this->fetchAll($this->select()->order("last_login_on desc")->limit($how_many));
		if (!is_null($tmp_users)) return $tmp_users->toArray();
		else return array();
	}

	/* Group: Static Methods */

	// /*
	//	Function: getAvatarPath
	//		Gets full path to avatar. You should always use this instead of referencing the avatar directly.
	//		Should be used both when storing an avatar and retrieving it.
	//
	//	Arguments:
	//		username - The username of the user whose avatar is needed.
	//		include_filename (optional) - Whether or not to include the filename or just the directory. Defaults to false.
	//
	//	Plugin Hooks:
	//		- *default_users_table_avatar_path* (filter) - Allows you to modify the default avatar path.
	//		param path - the default path to the avatar.
	// */
	// function getAvatarPath($username, $include_filename = false) {
	//	$path = RivetyCore_Registry::get('upload_path')."/".$username."/original";
	//	$params['path'] = $path;
	//	$params['filename'] = RivetyCore_Registry::get('avatar_filename');
	//	$params = $this->_rivety_plugin->doFilter("default_users_table_avatar_path", $params);
	//	if ($include_filename) {
	//		return $params['path']."/".$params['filename'];
	//	} else {
	//		return $params['path'];
	//	}
	// }

	/*
		Function: clearUserCache
			Deletes all of a particular user's images from the server cache.

		Arguments:
			username - The username of the user whose cache is to be cleared.
	*/
	static function clearUserCache($username)
	{
		$cache_path = RivetyCore_Filesystem::getPath('usercache', $username);
		if (file_exists($cache_path)) RivetyCore_Filesystem::SureRemoveDir($cache_path, false);
	}

	// /* Group: Private or Protected Methods */
	//
	// /*
	//	Function: parseTags
	// */
	// protected function parseTags($all_tags) {
	//	$tags = unserialize($all_tags);
	//	$uct = new UsertagTotals();
	//	if (is_array($tags)) {
	//		foreach ($tags as $tag) {
	//			$uct->insert(array('tag' => $tag));
	//		}
	//	}
	// }

}
