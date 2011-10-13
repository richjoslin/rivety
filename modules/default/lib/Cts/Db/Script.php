<?php

class Cts_Db_Script {

	public $errors = null;

	function Cts_Db_Script($module = null, $scriptname = null, $dbAdapter = null) {

		$this->errors = array();

		if (is_null($dbAdapter)) {
			$this->_db = Zend_Db_Table::getDefaultAdapter();
		} else {
			$this->_db = $dbAdapter;
		}

		if (!is_null($module) and !is_null($scriptname)) {
			return $this->run($module, $scriptname);
		}
	}

	function getScripts($module) {
		$scripts = array();
		$script_path = $this->getScriptPath($module);

		if (is_readable($script_path)) {

			$script_dir = dir($script_path);

			while (false !== ($entry = $script_dir->read())) {
				if (substr($entry, 0, 1) != ".") {
					$scripts[] = substr($entry, 0, strpos($entry, ".sql"));
				}
			}
			$script_dir->close();

		} else {
			$this->errors[] = "Script path is not readable.";
		}
		return $scripts;
	}

	function run($module, $scriptname) {
		$success = false;
		$ddl_file = $this->getScriptPath($module)."/".$scriptname.".sql";
		if (file_exists($ddl_file)) {
			$queries = preg_split('/;[\r\n]+/', file_get_contents($ddl_file), -1, PREG_SPLIT_NO_EMPTY);
			try {
				foreach ($queries as $query) {
					if (trim($query) != "") {						
						$this->_db->query($query);
					}
				}
				$success = true;
			} catch (Exception $e) {				
				$this->errors[] = $e->getMessage();				
			}
		} else {
			$this->errors[] = "Script file is missing.";
		}
		return $success;
	}

	function getScriptPath($module) {
		$obj_type = get_class($this->_db);
		$db_type = strtoupper(substr($obj_type, strlen("Zend_Db_Adapter_")));
		return Zend_Registry::get("basepath")."/modules/".$module."/sql/".$db_type;
	}

}
