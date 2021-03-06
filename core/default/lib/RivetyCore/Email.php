<?php

/*
	Class: RivetyCore_Email

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>
*/
class RivetyCore_Email {

	/* Group: Constructors */

	/*
		Constructor: RivetyCore_Email
			Sets a template path variable (_template_path), and instantiates a logger.

		Arguments:
			template_path - An optional string to override the default template path.
	*/
	function RivetyCore_Email($template_path = null, $module = "default")
	{
		$this->_smarty = new RivetyCore_View_Smarty();
		$smarty_config = Zend_Registry::get('smarty_config');
		foreach ($smarty_config as $key => $value)
		{
			if ($key != "plugins_dir")
			{
				$this->_smarty->$key = $value;
			}
			else
			{
				$plugin_dirs = array('plugins');
				if (is_array($value))
				{
					foreach ($value as $plugin_dir)
					{
						$plugin_dirs[] = $plugin_dir;
					}
				}
				else
				{
					$plugin_dirs[] = $value;
				}
				$this->_smarty->plugins_dir = $plugin_dirs;
			}
		}
		if (is_null($template_path))
		{

			$theme_locations = Zend_Registry::get('theme_locations');
			$email_path = "/global/email/";
			if ($module == "default")
			{
				$this->_smarty->template_dir = $theme_locations['frontend']['current_theme']['path'] . $email_path;
			}
			else
			{
				$this->_smarty->template_dir = $theme_locations['frontend']['current_theme']['path'] . "/tpl_controllers/" . $module . $email_path;
			}

		}
		else
		{
			$this->_smarty->template_dir = $template_path;
		}
	}

	/*
		Function: sendEmail
			Sends an email.

		Arguments:
			subject - The subject line of the email to send.
			to_address - The recipient email address to send the email to.
			template - A template file to use for the layout.
			params (optional) - An array of arbitrary parameters that get rendered as view variables, each of which can be used within the supplied template.
			to_name (optional) - The name of the recipient.
			isHtml (optional) - Sets whether the email is plain text (false) or HTML (true). Default is false.

		Returns: void
	*/
	function sendEmail($subject, $to_address, $template, $params = null, $to_name = null, $isHtml = false) {
		$useAuth = RivetyCore_Registry::get('smtp_use_auth');

		if (array_key_exists('from_email', $params)) {
			$site_from_email = $params['from_email'];
		} else {
			$site_from_email = RivetyCore_Registry::get('site_from_email');
		}

		// TODO - shouldn't this be from_name instead of from_email ?
		if (array_key_exists('from_name', $params)) {
			$site_from = $params['from_name'];
		} else {
			$site_from = RivetyCore_Registry::get('site_from');
		}

		$smtp = RivetyCore_Registry::get('smtp_server');
		$username = RivetyCore_Registry::get('smtp_username');
		$password = RivetyCore_Registry::get('smtp_password');
		$ssl = RivetyCore_Registry::get('smtp_ssl_type');  //tls
		$smtp_port = RivetyCore_Registry::get('smtp_port');

		$config = array();
		if ($useAuth == 1) {
			$config = array(
				'auth' => 'login',
          		'username' => $username,
          		'password' => $password,
				'ssl' => $ssl,
				'port' => (int)$smtp_port);
		}
		try {
          	$mailTransport = new Zend_Mail_Transport_Smtp($smtp, $config); // defines gmail smtp infrastructure as default for any email message originated by Zend_Mail.
          	Zend_Mail::setDefaultTransport($mailTransport);
			$mail = new Zend_Mail();
			foreach ($params as $key => $value) {
				$this->_smarty->assign($key, $value);
			}
			$message = $this->_smarty->fetch($template);

			if ($isHtml) {
				$mail->setBodyHtml($message);
			} else {
				$mail->setBodyText($message);
			}

			$mail->setFrom($site_from_email, $site_from);
			if (!is_null($to_name) && trim($to_name) != '') {
				$mail->addTo($to_address, $to_name);
			} else {
				$mail->addTo($to_address);
			}
			$mail->setSubject($subject);
			$mail->setReturnPath(RivetyCore_Registry::get('site_from_email'));
			$id_part = substr($site_from_email, strpos('@', $site_from_email));
			$message_id = md5(uniqid()).$id_part;
			//$mail->addHeader('Message-Id', $message_id);

			$mail->send();

		} catch (Exception $e) {
			RivetyCore_Log::report('email: could not send', $e, Zend_Log::ERR);
		}
	}

	/*
		Function: sendHtmlEmail
			Sends an HTML email. The same as using sendEmail with the isHtml boolean set to true (in fact, that's all this does).

		Arguments:
			subject - The subject line of the email to send.
			to_address - The recipient email address to send the email to.
			template - A template file to use for the layout.
			params (optional) -
			to_name (optional) -

		See Also:
			- <sendEmail>
	*/
	function sendHtmlEmail($subject, $to_address, $template, $params = null, $to_name = null) {
		$this->sendEmail($subject, $to_address, $template, $params, $to_name, true);
	}

}
