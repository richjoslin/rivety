<?php

/*
	Class: Install
		This is the installation controller. It's called via a Zend_Controller plugin called <InstallPlugin>
		that fires on the pre_dispatch event if there's no config.ini.

	About: Author
		Jaybill McCarthy

	About: License
		<http://rivety.com/docs/license>

	About: See Also
		<InstallPlugin>
*/
class InstallController extends Zend_Controller_Action
{

	/* Group: Instance Methods */

	/*
		Function: init
			Invoked automatically when an instance is created.
			Initializes the current instance.
			Also initializes the parent object (calls init() on the parent instance).

		Registry Values:
			theme_locations - theme locations as set by the bootstrap

		View Variables:
			site_name - temporary site name for installer
			default_admin_theme_path - filesystem path to the current admin theme
			default_admin_global_path - filesystem path to the current admin theme global includes
			default_admin_theme_url - url to the base of the current admin theme
			isAdminController - boolean set to true (so view uses correct theme)
			current_path - filesystem path to the template directory for the current controller
			site_name - set to "Rivety" for installer, used by default installer theme
	*/
	function init()
	{
		parent::init();

		$theme_locations = Zend_Registry::get("theme_locations");
		$template_path = $theme_locations['admin']['default_theme']['path'] . '/tpl_controllers';
		$this->view->setScriptPath($template_path);
		$this->view->default_admin_theme_path = $theme_locations['admin']['default_theme']['path'];
		$this->view->default_admin_global_path = $theme_locations['admin']['default_theme']['path'] . '/tpl_common';
		$this->view->default_admin_theme_url = $theme_locations['admin']['default_theme']['url'];
		$this->view->current_path = $template_path . "/" . $this->getRequest()->getControllerName();
        $this->view->isAdminController = true;
		$this->view->site_name = "Rivety";
	}

	/* Group: Actions */

	/*
		Function: secondstage
			This index action redirects to this action to set up some additional configuration items, set
			some resource permissions and force an automatic login. Then it redirects to the <finished> action.

		HTTP GET or POST Parameters:
			username - the username chosen in the index action. Used for automatic login
	*/
	function secondstageAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$appNamespace = new Zend_Session_Namespace('RivetyCore_Temp');
		$basepath = Zend_Registry::get('basepath');
		$config_table = new Config();
		$config_table->set('default', 'upload_path', $basepath."/uploads", true);
		$config_table->set('default', 'theme', 'default', true);
		$config_table->set('default', 'missing_image', $basepath . "/themes/frontend/default/images/image-missing.jpg", true);
		$config_table->set('default', 'site_url', 'http://' . $_SERVER['SERVER_NAME']);
		$config_table->set('default', 'salt', substr(md5(rand(1, 1000)), 0, 10));
		$config_table->cache();
		$username = $request->username;
		$users_table = new Users();
		$user = $users_table->fetchByUsername($username);
		$password = substr(md5(rand(50000, 100000)), 0, 8);
		if (!is_null($user))
		{
			$user->password = $password;
			$user->save();
			// $users_table->setMetaData($username, "is_installer", 1);
			$appNamespace->autoLogin = true;
       		$appNamespace->autoLoginUsername = $username;
       		$appNamespace->autoLoginPassword = $password;
			$appNamespace->autoLoginPasswordHash = md5($password);
		}
		else
		{
			die("Somehow the admin user didn't get created or didn't get sent with the request. This is bad. Really, really bad.");
		}
		$this->_redirect("/default/install/finished/username/" . $username);
	}

	/*
		Function: finished
			This is called when installation is complete.

		HTTP GET or POST Parameters:
			username - the username chosen in the <index> action

		View Variables:
			username - the username passed along from <secondstage>
			password - a randomly generated password

	*/
	function finishedAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$username = $request->username;
		$users_table = new Users();
		$user = $users_table->fetchByUsername($username);
		$password = substr(md5(rand(50000, 100000)), 0, 8);
		if (!is_null($user))
		{
			// TODO: check the referrer !
			// if this page is reloaded, the admin password is going to get reset to something random, and this page is going to get redirected - CRITICAL FIX

			$user->password = $password;
			$user->save();
			$this->view->username = $username;
			$this->view->password = $password;

			// we should never need this again, so we remove access to it.
			$roles_resources_table = new RolesResources();
			$where  = $roles_resources_table->getAdapter()->quoteInto("module = ? ", "default");
			$where .= $roles_resources_table->getAdapter()->quoteInto(" and controller = ? ", "Install");
			$roles_resources_table->delete($where);
			$modules_table = new Modules('core');
			$modules_table->upgradeDatabase('default');
		}
		else
		{
			die("Error creating admin user. Please check for errors in /logs/RivetyCore_log");
		}
		$this->view->admin_theme_url = "/core/default/views/admin/default";
		$this->view->admin_theme_global_path = Zend_Registry::get('basepath') . "/themes/admin/default/global";
	}

	/*
		Function: index
			Displays the Rivety installer. This action will be called if the /etc/config.ini is missing.
			It will gather the required installation variables, check that all the file/directory permissions
			are correct, write the config file and install the database.

		HTTP GET or POST Parameters:
			admin_email - email address of the admin user (if chosen)
			admin_username - username of the admin user (if chosen)
			asido_path - path to Asido image processing library
			smarty_path - path to Smarty template engine
			timezone - application default timezone
			zf_path - path to the Zend Framework
			db_host - database hostname
			db_name - database name
			db_user - database username
			db_pass - database password
			db_port - database port
			db_sock - the database socket
			errors - An array of error messages. Only exists if errors occurred.
			smarty_cache_dir - the directory smarty will use for caching
			smarty_compile_dir - the directory smarty will use for compiled templates

		View Variables:
			admin_email - email address of the admin user (if chosen)
			admin_username - username of the admin user (if chosen)
			asido_path - path to Asido image processing library
			smarty_path - path to Smarty template engine
			timezone - application default timezone
			zf_path - path to the Zend Framework
			db_host - database hostname
			db_name - database name
			db_user - database username
			db_pass - database password
			db_port - database port
			db_sock - the database socket
			errors - An array of error messages. Only exists if errors occurred.
			smarty_cache_dir - the directory smarty will use for caching
			smarty_compile_dir - the directory smarty will use for compiled templates
	*/
	function indexAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$basepath = Zend_Registry::get('basepath');
		$this->view->timezones = RivetyCore_Common::getTimeZonesArray();

		if ($this->getRequest()->isPost())
		{
			$errors = array();

			/*
			 * TODO: Check that smarty dirs are writeable, etc. dir is writable, etc. dir is NOT writeable after install, libraries exist,
			 * log level is set to something
			 */

			if (!file_exists($basepath . "/.htaccess"))
			{
				$errors[] = $this->_T("Missing .htaccess file in %s. Be sure to copy %s/template.htaccess and remove the word template from the filename.", array($basepath, $basepath));
			}

			$zf_version_class   = $request->zf_path . "/Zend/Version.php";
			$smarty_class_file  = $request->smarty_path . "/Smarty.class.php";
			$asido_class_file   = $request->asido_path . "/class.asido.php";
			$etc_dir            = $basepath . "/etc";
			$config_filename    = $etc_dir . "/config.ini";
			$tmp_path           = $request->tmp_path;
			$smarty_compile_dir = $tmp_path . "/view_compiles";
			$smarty_cache_dir   = $tmp_path . "/cache";
			$image_cache_dir    = $tmp_path . "/image_cache";
			$upload_path        = $basepath . "/uploads";
			$log_path           = $request->log_path;
			$module_cfg         = parse_ini_file($basepath . "/core/default/module.ini", true);

			if (!file_exists($zf_version_class))
			{
				$errors[] = $this->_T("Can't find Zend Framework in %s", $request->zf_path);
			}
			else
			{
				require_once($zf_version_class);
				if (Zend_Version::compareVersion($module_cfg['lib_versions']['zf']) > 0)
				{
					$errors[] = $this->_T("Rivety requires Zend Framework %s or higher. The supplied version is %s.", array($module_cfg['lib_versions']['zf'], Zend_Version::VERSION));
				}
			}
			if (!file_exists($smarty_class_file))
			{
				$errors[] = $this->_T("Can't find Smarty in %s", $request->RivetyCore_smarty_path);
			}
			else
			{
				$smarty_class_lines = explode("\n",file_get_contents($smarty_class_file));
				$strVersion = "* @version";
				foreach ($smarty_class_lines as $line)
				{
					if (strpos($line,$strVersion) !== false)
					{
						$found_smarty_version = trim(substr($line,strpos($line,$strVersion) + strlen($strVersion)));
						break;
					}
				}
				if (version_compare($module_cfg['lib_versions']['smarty'],$found_smarty_version) > 0)
				{
					$errors[] = $this->_T("Rivety requires Smarty Template Engine %s or higher. The supplied version is %s.", array($module_cfg['lib_versions']['smarty'], $found_smarty_version));
				}
			}
			if (!file_exists($asido_class_file))
			{
				$errors[] = $this->_T("Can't find Asido in %s.", $request->RivetyCore_asido_path);
			}
			else
			{
				require_once($asido_class_file);
				$asido = new Asido();
				if (version_compare($module_cfg['lib_versions']['asido'], $asido->version()) > 0)
				{
					$errors[] = $this->_T("Rivety requires Asido %s or higher. The supplied version is %s.", array($module_cfg['lib_versions']['asido'], $asido->version()));
				}
			}
			$dir_array = array($etc_dir,
				$tmp_path,
				$upload_path,
				$log_path
				);
			foreach ($dir_array as $dir)
			{
				if (!is_writable($dir))
				{
					$errors[] = $this->_T("Web server can't write to %s.", $dir);
				}
			}
			if ($request->admin_username == null)
			{
				$errors[] = $this->_T("Admin username cannot be blank.");
			}

			if ($request->admin_email == null)
			{
				$errors[] = $this->_T("Admin email cannot be blank.");
			}

			$cfg_array = array(
				"database" => array(
					"adapter" => "PDO_MYSQL",
					"params" => array(
						"host"     => $request->db_host,
						"dbname"   => $request->db_name,
						"username" => $request->db_user,
						"password" => $request->db_pass,
						"port"     => $request->db_port,
					)
				)
			);

			if (!is_null($request->db_sock))
			{
				$cfg_array['database']['params']['unix_socket'] = $request->db_sock;  // this is often something like /var/run/mysqld/mysqld.sock
			}

			$dbconfig = new Zend_Config($cfg_array);

			$db = Zend_Db::factory($dbconfig->database);

			try
			{
				if (count($errors) == 0)
				{
					$tables = $db->listTables();
					if (count($tables) > 0)
					{
						$errors[] = $this->_T("The specified database is not empty.");
					}
					// get the table creation script
					$ddl_file = $basepath . "/core/default/sql/" . $dbconfig->database->adapter . "/install.sql";
					if (file_exists($ddl_file)) {
						$queries = explode(";",file_get_contents($ddl_file));
						$db->beginTransaction();
						try
						{
							foreach ($queries as $query)
							{
								if (trim($query) != "")
								{
									$query = str_replace("@@@@ADMIN_USERNAME@@@@", $request->admin_username, $query);
									$query = str_replace("@@@@ADMIN_EMAIL@@@@", $request->admin_email, $query);
									$query = str_replace("@@@@CREATED_ON@@@@@", date("Y-m-d H:i:s"), $query);
									$db->query($query);
								}
							}
							$db->commit();
						}
						catch (Exception $e)
						{
							$db->rollBack();
							$errors[] = $e->getMessage();
						}
					}
					else
					{
						$errors[] = $this->_T("Database creation script not found.");
					}
				}
			}
			catch (Exception $e)
			{
				$errors[] = $e->getMessage();
			}

			if (count($errors) == 0)
			{
				// everything worked out okay, attempt to write the config file

				$config = array(
					"db.rivety.adapter"         => "PDO_MYSQL", // This should really be configurable, but it isn't yet.
					"db.rivety.config.host"     => $request->db_host,
					"db.rivety.config.dbname"   => $request->db_name,
					"db.rivety.config.username" => $request->db_user,
					"db.rivety.config.password" => $request->db_pass,
					"db.rivety.config.port"     => $request->db_port,
					"db.rivety.config.default"  => "true",
				);
				if (!is_null($request->db_sock))
				{
					$config['db.rivety.config.unix_socket'] = $request->db_sock;
				}
				$config_file .= RivetyCore_ConfigFile::makeSection("databases", "Database Settings", "This is the default database.", $config);

				$RivetyCore_config = array(
					"timezone"        => $request->RivetyCore_timezone,
					"launched"        => "1",
					"prelaunch_url"   => "http://google.com",
					"allowed_ips"     => "127.0.0.1",
					"zf_path"         => $request->zf_path,
					"smarty_path"     => $request->smarty_path,
					"asido_path"      => $request->asido_path,
					"image_cache_dir" => $image_cache_dir,
					"log_filename"    => $log_path."/RivetyCore_log",
					"log_level"       => "6",
					"addtl_includes"  => "",
				);

				$config_file .= RivetyCore_ConfigFile::makeSection("application", "Application Settings", "These are the application specific settings.", $RivetyCore_config);

				// create directories if needed
				if (!file_exists($smarty_compile_dir))
				{
					mkdir($smarty_compile_dir, 0777, true);
				}

				if (!file_exists($smarty_cache_dir))
				{
					mkdir($smarty_cache_dir, 0777, true);
				}

				if (!file_exists($image_cache_dir))
				{
					mkdir($image_cache_dir, 0777, true);
				}

				$smarty_config = array(
					"config.compile_dir" => $smarty_compile_dir,
					"config.cache_dir"   => $smarty_cache_dir,
				);

				$config_file .= RivetyCore_ConfigFile::makeSection("smarty", "Smarty Settings", "These are the settings for the Smarty template engine.", $smarty_config);

				if (file_put_contents($config_filename, $config_file) === false)
				{
					$this->view->config_file = $config_file;
					$this->view->config_filename = $config_filename;
					$this->view->success = "Database installed, but could not write config file. Please create the file \"" . $config_filename . "\" and paste this following into it:";
				}
				else
				{
					$this->_redirect("/default/install/secondstage/username/" . $request->admin_username);
				}
			}
			else
			{
				$this->view->errors          = $errors;
				$this->view->db_host         = $request->db_host;
				$this->view->db_name         = $request->db_name;
				$this->view->db_user         = $request->db_user;
				$this->view->db_pass         = $request->db_pass;
				$this->view->db_port         = $request->db_port;
				$this->view->db_sock         = $request->db_sock;
				$this->view->admin_username  = $request->admin_username;
				$this->view->admin_email     = $request->admin_email;
				$this->view->timezone        = $request->timezone;
				$this->view->zf_path         = $request->zf_path;
				$this->view->smarty_path     = $request->smarty_path;
				$this->view->asido_path      = $request->asido_path;
				$this->view->tmp_path        = $request->tmp_path;
				$this->view->log_path        = $request->log_path;
			}
		}
		else
		{
			$this->view->db_host         = "localhost";
			$this->view->db_name         = "rivety";
			$this->view->db_user         = "root";
			$this->view->db_pass         = "";
			$this->view->db_port         = "3306";
			$this->view->db_sock         = "";
			$this->view->admin_username  = "admin";
			$this->view->timezone        = "America/Los_Angeles";
			$this->view->zf_path         = $basepath . "/lib/ZendFramework/library";
			$this->view->smarty_path     = $basepath . "/lib/Smarty/libs";
			$this->view->asido_path      = $basepath . "/lib/Asido";
			$this->view->tmp_path        = $basepath . "/tmp";
			$this->view->log_path        = $basepath . "/logs";
		}
		$this->view->admin_theme_url = "/core/default/views/admin";
		$this->view->admin_theme_global_path = $basepath . "/core/default/views/admin/tpl_common";
	}

	protected function _T($key, $replace = null)
	{
		// we're not actually doing the translation in the installer. we may some day.
		return RivetyCore_Translate::translate(null, "default", $key, $replace, false);
	}

}
