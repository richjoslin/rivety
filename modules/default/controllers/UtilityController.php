<?php

/*
	Class: Utility

	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>

*/
class UtilityController extends  Cts_Controller_Action_Admin {
	/*
		Function: serialize
	*/
	function serializeAction() {
		$request = new Cts_Request($this->getRequest());
		if ($this->getRequest()->isPost()) {
			$output = array();
			for ($i = 1; $i <= 7; $i++) {
				if ($request->{"value".$i} != "") {
					$output[] = $request->{"value".$i};
				}
			}
			$this->view->result = serialize($output);
		}
	}

	/*
		Function: serializeassociative
	*/
	function serializeassociativeAction() {
		$request = new Cts_Request($this->getRequest());
		if ($this->getRequest()->isPost()) {
			$output = array();
			for ($i = 1; $i <= 7; $i++) {
				if ($request->{"key".$i} != "") {
					$output[$request->{"key".$i}] = $request->{"value".$i};
				}
			}
			$this->view->result = serialize($output);
		}
	}

	/*
		Function: unserialize
	*/
	function unserializeAction() {
		$request = new Cts_Request($this->getRequest());
		if ($this->getRequest()->isPost()) {
			$this->view->results = unserialize($request->serialized_array);
		}
	}
	
}
