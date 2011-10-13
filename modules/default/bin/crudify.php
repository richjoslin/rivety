#!/usr/bin/php
<?php

/*
	IMPORTANT:
		This script assumes your table is named with the exact module name as the prefix followed by an underscore.
*/

require_once('cli_header.php');

$is_admin = false;

if (!array_key_exists("1", $argv)) {
	die("Usage: crudify.php [table_name] [optional bool is_admin, default false]\n");
} else {
	$table_name = $argv[1];
	if (array_key_exists("2", $argv) && !empty($argv[2])) {
		$is_admin = (bool)$argv[2];
	}
}

echo("Table Name: ".$table_name."\n");

$module_name = substr($table_name, 0, strpos($table_name, "_"));
$module_name_uc = ucfirst($module_name);
$name = ucfirst(substr($table_name, strpos($table_name, "_") + 1));
$name = str_replace("_", "", $name);
$object_name = $module_name_uc.$name;
$abstract_table_object_name = $module_name_uc."_Db_Table";
if ($is_admin) {
	$controller_name = $module_name_uc."_".$name."adminController";
} else {
	$controller_name = $module_name_uc."_".$name."Controller";
}
$table_object_var = strtolower($name."_table");
$row_object_var = strtolower($name)."_row";

$registry = Zend_Registry::getInstance();
// $theme = $registry->get("theme");
$theme = 'default';
echo("Theme:".$theme."\n");
if ($is_admin) {
	$theme_dir = $basepath."/themes/admin/".$theme."/modules/".$module_name."/".strtolower($name).'admin';
} else {
	$theme_dir = $basepath."/themes/frontend/".$theme."/modules/".$module_name."/".strtolower($name);
}
if (!is_dir($theme_dir)) {
	mkdir($theme_dir, 0777, true);
}

// Get table info
$db = Zend_Db_Table::getDefaultAdapter();
$all_tables = $db->listTables();
$columns = array();
$pk = null;
$required = array();
$identity_col = null;
if (in_array($table_name, $all_tables)) {
	echo("Found table ".$table_name.".\n");
	$table_desc = $db->describeTable($table_name);	
	foreach ($table_desc as $key => $column) {
		$columns[$key]['type'] = $column['DATA_TYPE']; 
		if ($column['IDENTITY']) {
			$identity_col = $key;
		}
		if ($column['PRIMARY']) {
			if (is_null($pk)) {
				$pk = $key;	
			} else {
				if (is_array($pk)) {
					$pk[] = $key;
				} else {
					$pk = array($pk, $key);
				}
			}
		}
		if (!$column['NULLABLE'] and !in_array($key, $required) and !$column['IDENTITY']) {
			$required[] = $key;
		}
	}
} else {
	die("Table ".$table_name." does not exist.\n");
}

// create new model class
$model_class = new Zend_CodeGenerator_Php_Class();
$model_class->setName($object_name)->setExtendedClass($abstract_table_object_name)->setProperties(array(
	array(
		'name'         => '_name',
		'visibility'   => 'protected',
		'defaultValue' => $table_name,
	),
	array(
		'name'         => '_primary',
		'visibility'   => 'protected',
		'defaultValue' => $pk,
	),
));
$model_file = new Zend_CodeGenerator_Php_File();
$model_file->setClass($model_class);

// create new controller class
$controller_class = new Zend_CodeGenerator_Php_Class();
if ($is_admin) {
	$controller_class->setName($controller_name)->setExtendedClass("Cts_Controller_Action_Admin");
} else {
	$controller_class->setName($controller_name)->setExtendedClass("Cts_Controller_Action_Abstract");
}

if (!is_array($pk)) {

	// init method - required for all Communitas controller classes
	$init_method = new Zend_CodeGenerator_Php_Method();
	$init_method_body = "\t\tparent::init();";
	$init_method->setName('init')->setBody($init_method_body);
	$controller_class->setMethod($init_method);

	// edit action
	$edit_method = new Zend_CodeGenerator_Php_Method();
	$edit_method_body = file_get_contents($basepath."/modules/default/extras/crudify_templates/editAction.txt");
	$data_array_string = "";
	$data_validation_string = "";
	$error_view_variables ="";
	$view_variables="";
	foreach ($columns as $colname => $column) {
		if ($colname != $identity_col) {
			$error_view_variables .= "\t\t\$"."this->view->".$colname." = \$request->".$colname .";\n";
			$data_array_string .= "\t\t\t\t\"".$colname."\" => \$request->".$colname.",\n";
		}
		$view_variables .=  "\t\t\$"."this->view->".$colname." = \$".$row_object_var."->".$colname.";\n";
		if (in_array($colname, $required)) {
			$data_validation_string .= "\t\$request->addValidator(\"".$colname."\");\n";
		}
	}
	$edit_action_replacements = array(
		"DATA_VALIDATION"      => $data_validation_string,
		"TABLE_OBJECT_VAR"     => $table_object_var,
		"TABLE_CLASSNAME"      => $object_name,
		"ROW_OBJECT_VAR"       => $row_object_var,
		"THE_ID"               => $identity_col,
		"DATA_ARRAY_STRING"    => $data_array_string,
		"ERROR_VIEW_VARIABLES" => $error_view_variables,
		"VIEW_VARIABLES"       => $view_variables,
		"OBJECT_NICENAME"      => $name,
	);
	$edit_method_body = Cts_Common::replaceWithArray($edit_method_body, $edit_action_replacements);
	$edit_method->setName('editAction')->setBody($edit_method_body);
	$controller_class->setMethod($edit_method);

	// load edit template
	$edit_template = file_get_contents($basepath."/modules/default/extras/crudify_templates/edit.tpl");
	$form_fields = "";
	$form_field_template = file_get_contents($basepath."/modules/default/extras/crudify_templates/editTextInput.tpl");
	foreach ($columns as $colname => $column) {
		$field_string = $form_field_template;
		if ($colname != $identity_col) {
			$input_replacements = array(
				"FIELD_LABEL"    => ucfirst($colname),
				"FIELD_VAR_NAME" => $colname,
			);			
			$field_string = Cts_Common::replaceWithArray($field_string, $input_replacements);
			$form_fields .= $field_string;
		}
	}
	if ($is_admin) {
		$form_action = "/".$module_name."/".strtolower($name)."admin/edit";
		$theme_global_path_var_name = "admin_theme_global_path";
		$index_url = "/".$module_name."/".strtolower($name)."admin/index";
		$delete_url = "/".$module_name."/".strtolower($name)."admin/delete";
	} else {
		$form_action = "/".$module_name."/".strtolower($name)."/edit";
		$theme_global_path_var_name = "theme_global_path";
		$index_url = "/".$module_name."/".strtolower($name)."/index";
		$delete_url = "/".$module_name."/".strtolower($name)."/delete";
	}
	$edit_template_replacements = array(
		"THE_ID"                     => $identity_col,
		"THEME_GLOBAL_PATH_VAR_NAME" => $theme_global_path_var_name,
		"FORM_ACTION"                => $form_action,
		"INDEX_URL"                  => $index_url,	
		"FORM_FIELDS"                => $form_fields,
		"DELETE_URL"                 => $delete_url,
	);
	$edit_template = Cts_Common::replaceWithArray($edit_template, $edit_template_replacements);

	file_put_contents($theme_dir."/edit.tpl", $edit_template);

	// add index action
	$index_method = new Zend_CodeGenerator_Php_Method();
	$index_method_body = file_get_contents($basepath."/modules/default/extras/crudify_templates/indexAction.txt");
	$index_action_replacements = array(
		"TABLE_OBJECT_VAR" => $table_object_var,
		"TABLE_CLASSNAME"  => $object_name,
		"ROW_OBJECT_VAR"   => $row_object_var,
		"THE_ID"           => $identity_col,
		"OBJECT_NICENAME"  => $name,
		"ROWSET_VAR"       => strtolower($name),
		"INDEX_URL"        => $index_url,
	);
	$index_method_body = Cts_Common::replaceWithArray($index_method_body, $index_action_replacements);
	$index_method->setName('indexAction')->setBody($index_method_body);
	$controller_class->setMethod($index_method);

	// load index template
	$index_template = file_get_contents($basepath."/modules/default/extras/crudify_templates/index.tpl");
	if ($is_admin) {
		$index_template_replacements = array(
			"OBJECT_NICENAME"	=> $name,
			"THEME_GLOBAL_PATH_VAR_NAME" => "admin_theme_global_path",
			"CREATE_NEW_URL"	=> "/".$module_name."/".strtolower($name)."admin/edit",
			"ROWSET_VAR"		=> strtolower($name),
			"THE_ID"			=> $identity_col,
			"INDEX_URL" 		=> $index_url,
		);
	} else {
		$index_template_replacements = array(
			"OBJECT_NICENAME"	=> $name,
			"THEME_GLOBAL_PATH_VAR_NAME" => "theme_global_path",
			"CREATE_NEW_URL"	=> "/".$module_name."/".strtolower($name)."/edit",
			"ROWSET_VAR"		=> strtolower($name),
			"THE_ID"			=> $identity_col,
			"INDEX_URL" 		=> $index_url,
		);
	}
	$index_template = Cts_Common::replaceWithArray($index_template, $index_template_replacements);

	file_put_contents($theme_dir."/index.tpl", $index_template);

	// add delete method
	$delete_method = new Zend_CodeGenerator_Php_Method();
	$delete_method_body = file_get_contents($basepath."/modules/default/extras/crudify_templates/deleteAction.txt");
	$delete_action_replacements = array(
		"TABLE_OBJECT_VAR" 	=> $table_object_var,
		"TABLE_CLASSNAME" 	=> $object_name,
		"ROW_OBJECT_VAR"	=> $row_object_var,
		"THE_ID"			=> $identity_col,
		"OBJECT_NICENAME"	=> $name,
		"ROWSET_VAR"		=> strtolower($name),	
		"DELETE_URL" 		=> $delete_url,	
		"INDEX_URL" 		=> $index_url,
	);
	$delete_method_body = Cts_Common::replaceWithArray($delete_method_body, $delete_action_replacements);	
	$delete_method->setName('deleteAction')->setBody($delete_method_body);
	$controller_class->setMethod($delete_method);	

	// load delete template
	$delete_template = file_get_contents($basepath."/modules/default/extras/crudify_templates/delete.tpl");	
	if ($is_admin) {
		$delete_template_replacements = array(
			"OBJECT_NICENAME"            => $name,
			"THEME_GLOBAL_PATH_VAR_NAME" => "admin_theme_global_path",
			"CREATE_NEW_URL"             => "/".$module_name."/".strtolower($name)."admin/edit",
			"ROWSET_VAR"                 => strtolower($name),
			"THE_ID"                     => $identity_col,
			"DELETE_URL"                 => $delete_url,
			"INDEX_URL"                  => $index_url,
		);
	} else {
		$delete_template_replacements = array(
			"OBJECT_NICENAME"            => $name,
			"THEME_GLOBAL_PATH_VAR_NAME" => "theme_global_path",
			"CREATE_NEW_URL"             => "/".$module_name."/".strtolower($name)."/edit",
			"ROWSET_VAR"                 => strtolower($name),
			"THE_ID"                     => $identity_col,
			"DELETE_URL"                 => $delete_url,
			"INDEX_URL"                  => $index_url,
		);
	}
	$delete_template = Cts_Common::replaceWithArray($delete_template, $delete_template_replacements);

	file_put_contents($theme_dir."/delete.tpl", $delete_template);

	$controller_file = new Zend_CodeGenerator_Php_File();
	$controller_file->setClass($controller_class);
}

// Render the generated files
file_put_contents($basepath."/modules/".$module_name."/models/".$object_name.".php", $model_file->generate());

if ($is_admin) {
	file_put_contents($basepath."/modules/".$module_name."/controllers/".$name."adminController.php", $controller_file->generate());
} else {
	file_put_contents($basepath."/modules/".$module_name."/controllers/".$name."Controller.php", $controller_file->generate());
}
