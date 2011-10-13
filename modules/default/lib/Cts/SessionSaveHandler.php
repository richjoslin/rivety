<?php

/*
	Class: Cts_SessionSaveHandler

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

	About: See Also
		Zend_Session_SaveHandler_Interface
*/
class Cts_SessionSaveHandler implements Zend_Session_SaveHandler_Interface {

	/* Group: Instance Methods */

	/*
		Function: close
			TBD
	*/
	public function close() {
		//don't need to do anything, just has to be defined for the interface.
	}

	/*
		Function: destroy
			TBD

		Arguments:
			id - TBD

		Returns:
			void
	*/
	public function destroy($id) {
		$sessions_table = new Sessions();
		$where = $sessions_table->getAdapter()->quoteInto('id = ?', $id);
		Cts_Log::info("Session handler: deleted session id " .$id);
		$sessions_table->delete($where);
	}

	/*
		Function: gc
			TBD

		Arguments:
			lifetime - TBD

		Returns:
			void
	*/
	public function gc($lifetime) {
		$sessions_table = new Sessions();
		$where = "expiration < " . time() - (int)Cts_Registry::get('session_timeout');
		Cts_Log::info("Session handler: deleted sessions: " .$where);
		//$sessions_table->delete($where);
	}

	/*
		Function: open
			TBD

		Arguments:
			save_path - TBD
			name - TBD

		Returns:
			void
	*/
	public function open($save_path, $name) {
		//don't need to do anything, just has to be defined for the interface.
	}

	/*
		Function: read
			TBD

		Arguments:
			id - TBD

		Returns:
			Either a row from the sessions table or false.
	*/
	public function read($id) {
		$sessions_table = new Sessions();
		$where = $sessions_table->getAdapter()->quoteInto('id = ?', $id);
		$session = $sessions_table->fetchRow($where);
		if (count($session) > 0) {
			$value = $session->value;
		} else {
			$value = false;
		}
		return $value;
	}

	/*
		Function: write

		Arguments:
			id - TBD
			value - TBD

		Returns:
			void
	*/
	public function write($id, $value) {
		$sessions_table = new Sessions();
		$lifetime = (int)Cts_Registry::get('session_timeout');
		$expiration = time() + $lifetime;
		
		$data = array(
			'id' => $id,
			'value' => $value,
			'expiration' => $expiration,
		);
		
		$where = $sessions_table->getAdapter()->quoteInto('id = ?', $id);
		if($sessions_table->getCountByWhereClause($where) > 0){
			
			$sessions_table->update($data, $where);
			Cts_Log::info("Session handler: updated session " .$id);
		} else {			
			$id = $sessions_table->insert($data);
			Cts_Log::info("Session handler: updating session " .$id);
		}
	}
}
