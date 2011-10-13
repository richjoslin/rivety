<?php
/*
	Class: InstallPlugin
		This plugin is instatiated if there is no config.ini in the /etc directory of the app.
		It's used to install the application. Its sole funtion is to reroute any request made to 
		the app to the install controller.
		
	About: Author
		Jaybill McCarthy

	About: License
		<http://communit.as/docs/license>
		
	About: See Also
		<Install>	

*/
 
class InstallPlugin extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {			
			$request->setModuleName('default');
			$request->setControllerName('install');
			$request->setActionName('index');
  }
}
