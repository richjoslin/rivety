<?php

class ImageadminController extends RivetyCore_Controller_Action_Admin
{
	var $_allowed_extensions;

	public function init()
	{
		$this->_allowed_extensions = explode(',', RivetyCore_Registry::get('photo_browser_allowed_extensions'));
		parent::init();
	}

	public function indexAction()
	{
		$errors = array();
		$request = new RivetyCore_Request($this->getRequest());
		// $base_path = RivetyCore_Registry::get('upload_path') . "/" . $this->_identity->username;
		$base_path = RivetyCore_Registry::get('upload_path') . "/rivetycommon";

		/* PROCESS DELETE */

		if (
			$this->getRequest()->isPost()
			&& $request->has("files_to_delete")
			&& !empty($request->files_to_delete)
			&& $request->has("delete_from_folder")
			&& !empty($request->delete_from_folder)
		)
		{
			$files_to_delete = explode(',', $request->files_to_delete);
			$delete_from_folder = $request->delete_from_folder;
			foreach ($files_to_delete as $filename)
			{
				$filename = str_replace($delete_from_folder . "_", "", $filename);
				unlink($base_path . "/" . $delete_from_folder . "/" . $filename);
			}
			$this->view->success = "Images deleted successfully.";
		}

		/* PROCESS UPLOAD */

		if (
			$this->getRequest()->isPost()
			&& !empty($_FILES["files_to_upload"])
			&& $request->has("upload_to_folder")
			&& !empty($request->upload_to_folder)
		)
		{
			$upload_to_folder = $request->upload_to_folder;
			$uploader = new Zend_File_Transfer_Adapter_Http();
			$uploader->setDestination($base_path . "/" . $upload_to_folder);
			$uploader->addValidator('IsImage', true);
			$width = RivetyCore_Registry::get("photo_dims_" . $upload_to_folder . "_width");
			$height = RivetyCore_Registry::get("photo_dims_" . $upload_to_folder . "_height");
			$dimLimits = array();
			if (!empty($width))
			{
				$dimLimits["minwidth"] = $width;
				$dimLimits["maxwidth"] = $width;
			}
			if (!empty($height))
			{
				$dimLimits["minheight"] = $height;
				$dimLimits["maxheight"] = $height;
			}
			$uploader->addValidator('ImageSize', false, $dimLimits);
			$uploader->addValidator('FilesSize', false, array('min' => '1B', 'max' => '100MB'));
			if ($uploader->isValid())
			{
				umask(0);
				$config = parse_ini_file(str_replace('/index.php', '/etc/config.ini', $_SERVER['SCRIPT_FILENAME']));
				$cache_dir = $config['image_cache_dir'] . "/rivetycommon";
				foreach ($uploader->getFileInfo() as $info)
				{
					$new_filename = str_replace(" ", "-", strtolower($info['name']));
					$new_filename = preg_replace("/[^\w\.-]/", "", $new_filename);
					$uploader->addFilter("Rename", array("source" => $info['tmp_name'], "target" => $new_filename, "overwrite" => true));
					// clear the image cache for anything with a similar filename
					foreach (glob($cache_dir . '/*' . $new_filename . '*') as $filename)
					{
						unlink($filename);
					}
					if (!$uploader->receive($info["name"]))
					{
						$errors = array_merge($errors, $uploader->getMessages());
					}
				}
				if (empty($errors))
				{
					$this->view->success = "Images uploaded successfully.";
				}
			}
			else
			{
				$errors = array_merge($errors, $uploader->getMessages());
			}
		}

		// filter out Zend's sloppy error messages
		$filtered_errors = array();
		foreach ($errors as $key => $error)
		{
			if ($key == "fileExtensionFalse") $error = "You may only upload photos that have the .jpg file extension.";
			$filtered_errors[] = $error;
		}
		$errors = $filtered_errors;

		$this->view->errors = $errors;

		$uploads = array();

		$dir = new DirectoryIterator($base_path);
		foreach ($dir as $file_info_1)
		{
			if ($file_info_1->isDir() && !$file_info_1->isDot() && $file_info_1->__toString() != ".svn")
			{
				$dir_name = $file_info_1->__toString();
				$uploads[$dir_name] = array();
				$uploads[$dir_name]["friendly_name"] = ucwords(str_replace("_", " ", $dir_name));
				$subdir = new DirectoryIterator($base_path."/".$dir_name);
				foreach ($subdir as $file_info_2)
				{
					$extension = pathinfo($file_info_2, PATHINFO_EXTENSION);
					if (!$file_info_2->isDir() && !$file_info_2->isDot() && in_array(strtolower($extension), $this->_allowed_extensions))
					{
						$uploads[$dir_name]["filenames"][] = $file_info_2->__toString();
					}
				}
			}
		}
		$params = array('uploads' => $uploads);
		$params = $this->_rivety_plugin->doFilter("default_imageadmin_index_upload_tree", $params); // FILTER HOOK
		$uploads = $params['uploads'];
		$this->view->uploads = $uploads;

		// $this->view->notice = 'Warning: Photos uploaded with the same filename as an existing photo will automatically overwrite the old photo.';

		$this->view->breadcrumbs = array('Manage Photos' => null);
	}

	public function listAction()
	{
		$request = new RivetyCore_Request($this->getRequest());
		$base_path = RivetyCore_Registry::get('upload_path') . "/rivetycommon";
		if (!$request->has("folder") || empty($request->folder)) die("error - folder is empty or doesn't exist");
		$photos = array();
		$dir = new DirectoryIterator($base_path . "/" . $request->folder);
		foreach ($dir as $file_info)
		{
			$extension = pathinfo($file_info, PATHINFO_EXTENSION);
			if (!$file_info->isDir() && !$file_info->isDot() && in_array(strtolower($extension), $this->_allowed_extensions))
			{
				$photos[] = $file_info->__toString();
			}
		}
		natsort($photos);
		$photos = array_values($photos);
		if ($this->format == 'json') die(Zend_Json::encode($photos));
	}

}
