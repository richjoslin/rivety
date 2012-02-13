#!/usr/bin/php
<?php

/*
	IMPORTANT:
		This script assumes your table is named with the exact module name as the prefix followed by an underscore.
*/

require_once('cli_header.php');

function file_write($filename, $contents)
{
	echo("Writing " . $filename . " to disk.\n");
	file_put_contents($filename, $contents);
}

$is_admin = false;
$dry_run = false;

if (!array_key_exists("1", $argv))
{
	die("Usage: crudify.php [table_name] [optional bool is_admin, default false] [optional bool dry_run, default false] [optional template_path] [optional output_basedir]\n");
}
else
{
	$table_name = $argv[1];

	// is_admin flag
	if (array_key_exists("2", $argv) && !empty($argv[2])) $is_admin = (bool)$argv[2];

	// dry_run flag
	if (array_key_exists("3", $argv) && !empty($argv[3])) $dry_run = (bool)$argv[3];

	// template_path
	if (array_key_exists("4", $argv) && !empty($argv[4])) $crud_template_path = trim($argv[3]);

	// output basedir
	if (array_key_exists("5", $argv) && !empty($argv[5])) $output_basedir = trim($argv[4]);
}

if ($dry_run) echo("Executing dry run...\n\n");
else echo("Executing the real thing (not a dry run)...\n\n");

echo("Database Table Name:          " . $table_name . "\n");

$module_name = substr($table_name, 0, strpos($table_name, "_"));

echo("Rivety Module Name:           " . $module_name . "\n");

$module_name_uc = ucfirst($module_name);

echo("Rivety Module Name Upper:     " . $module_name_uc . "\n");

$entity_name_underscored = substr($table_name, strpos($table_name, "_") + 1);

echo("Entity Name Underscored:      " . $entity_name_underscored . "\n");

$entity_nicename = ucwords(str_replace("_", " ", $entity_name_underscored));

echo("Entity Nice Name:             " . $entity_nicename . "\n");

$entity_name_PascalCase = str_replace(" ", "", ucwords(str_replace("_", " ", $entity_name_underscored)));

echo("Entity Nice Name No Spaces:   " . $entity_name_PascalCase . "\n");

$model_class_name = $module_name_uc . $entity_name_PascalCase;

echo("Model Class Name:             " . $model_class_name . "\n");

$entity_name_oneword = str_replace("_", "", $entity_name_underscored);

echo("Entity Name One Word:         " . $entity_name_oneword . "\n");

$abstract_table_object_name = $module_name_uc . "_Db_Table";

echo("Abstract Class Name:          " . $abstract_table_object_name . "\n");

if (empty($output_basedir)) $output_basedir = $basepath;

echo("Rivety Base Directory:        " . $output_basedir . "\n");

if (empty($crud_template_path)) $crud_template_path = $basepath . "/core/default/extras/crudify_templates";

echo("Source Template Directory:    " . $crud_template_path . "\n");

$controller_name = $entity_name_oneword . ($is_admin ? "admin" : "");

echo("Output Controller Name:       " . $controller_name . "\n");

$controller_class_name = $module_name_uc . "_" . ucfirst($entity_name_oneword) . ($is_admin ? "adminController" : "Controller");

echo("Output Controller Class Name: " . $controller_class_name . "\n");

$row_var_name = strtolower($entity_name_underscored) . "_row";

echo("Row Variable Name:            " . $row_var_name . "\n");

$registry = Zend_Registry::getInstance();

$theme_dir = $output_basedir . "/modules/" . $module_name . "/views/" . ($is_admin ? "admin" : "frontend") . "/tpl_controllers/" . strtolower($entity_name_oneword);

if ($is_admin) $theme_dir .= 'admin';

echo("Output Theme Directory:       " . $theme_dir . "\n");

if (is_dir($theme_dir)) die("\nERROR: The theme directory already exists. To replace it, delete or rename the old directory before running this script.\n\n");
elseif (!$dry_run) mkdir($theme_dir, 0777, true);

$db = Zend_Db_Table::getDefaultAdapter();
$all_tables = $db->listTables();
$columns = array();
$pk = null;
$reserved = array('id', 'sort_order', 'created_on', 'modified_on', 'deleted_on');
$required = array();
$id_column_name = null;
if (in_array($table_name, $all_tables))
{
	$table_desc = $db->describeTable($table_name);
	foreach ($table_desc as $key => $column)
	{
		$columns[$key]['type'] = $column['DATA_TYPE'];
		if ($column['IDENTITY']) $id_column_name = $key;
		if ($column['PRIMARY'])
		{
			if (is_null($pk)) $pk = $key;
			else
			{
				if (is_array($pk)) $pk[] = $key;
				else $pk = array($pk, $key);
			}
		}
		if (!$column['NULLABLE'] and !in_array($key, $required) and !$column['IDENTITY'])
		{
			$required[] = $key;
		}
	}
}
else die("\nERROR: Could not find a table named " . $table_name . ".\n\n");

$model_class = new Zend_CodeGenerator_Php_Class();
$model_class->setName($model_class_name)->setExtendedClass($abstract_table_object_name)->setProperties(array(
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

$controller_class = new Zend_CodeGenerator_Php_Class();
$controller_class->setName($controller_class_name)->setExtendedClass("RivetyCore_Controller_Action_" . ($is_admin ? "Admin" : "Abstract"));

$replacements = array(
	'ENTITY_NAME' => $entity_name_underscored,
	'ENTITY_NICENAME_LOWERCASE' => strtolower($entity_nicename),
	'ENTITY_NICENAME' => $entity_nicename,
	'MODEL_CLASS_NAME' => $model_class_name,
	'ROW_VAR_NAME' => $row_var_name,
	'ROWSET_VAR_NAME' => $row_var_name . 'set',
	'INDEX_MCA' => $module_name . '_' . $controller_name . '_index',
	'ID_COLUMN_NAME' => $id_column_name,
	'ID_URL_KEY' => $entity_name_underscored . '_' . $id_column_name,
	'INDEX_URL' => '/' . $module_name . '/' . $controller_name . '/index/',
	'PREVIEW_URL' => '/' . $module_name . '/' . $controller_name . '/preview/' . $entity_name_underscored . '_' . $id_column_name . '/{$' . $entity_name_underscored . '.' . $id_column_name . '}/',
	'CREATE_NEW_URL' => '/' . $module_name . '/' . $controller_name . '/edit/',
	'EDIT_URL' => '/' . $module_name . '/' . $controller_name . '/edit/' . $entity_name_underscored . '_' . $id_column_name . '/{$' . $entity_name_underscored . '.' . $id_column_name . '}/',
	'DELETE_URL' => '/' . $module_name . '/' . $controller_name . '/delete/' . $entity_name_underscored . '_' . $id_column_name . '/{$' . $entity_name_underscored . '.' . $id_column_name . '}/',
	'THEME_GLOBAL_PATH_VAR_NAME' => ($is_admin ? 'admin_' : '') . 'theme_global_path',
	'FORM_ID' => 'rivety-' . ($is_admin ? 'admin' : 'main') . '-form',
);

// TODO: make this work for compound primary keys

if (!is_array($pk))
{
	// INDEX ACTION

	$index_method = new Zend_CodeGenerator_Php_Method();
	$index_method_body = file_get_contents($crud_template_path . "/indexAction.txt");
	$index_method_body = RivetyCore_Common::replaceWithArray($index_method_body, $replacements);
	$index_method->setName('indexAction')->setBody($index_method_body);
	$controller_class->setMethod($index_method);
	$index_template = file_get_contents($crud_template_path . "/index.tpl");
	$index_template = RivetyCore_Common::replaceWithArray($index_template, $replacements);

	// VIEW ACTION

	$preview_method = new Zend_CodeGenerator_Php_Method();
	$preview_method_body = file_get_contents($crud_template_path . "/previewAction.txt");
	$preview_method_body = RivetyCore_Common::replaceWithArray($preview_method_body, $replacements);
	$preview_method->setName('previewAction')->setBody($preview_method_body);
	$controller_class->setMethod($preview_method);
	$preview_template = file_get_contents($crud_template_path . "/preview.tpl");
	$preview_template = RivetyCore_Common::replaceWithArray($preview_template, $replacements);

	// EDIT ACTION

	$post_value_assignment = "";
	$request_validation = "";
	$view_variables = "";

	foreach ($columns as $colname => $column)
	{
		if (!in_array($colname, $reserved)) // reserved field names don't need form fields or validation
		{
			if ($colname != $id_column_name)
			{
				$post_value_assignment .= "\t\t\t\t" . '$' . $row_var_name . '[\'' . $colname . '\'] = $request->has(\'' . $colname . '\') ? $request->' . $colname . ' : null' . ";\n";
			}
			if (in_array($colname, $required))
			{
				$request_validation .= "\t\$request->addValidator('" . $colname . "', 'Please enter a value for the " . $colname . " field.');\n";
			}
		}
	}
	$replacements['POST_VALUE_ASSIGNMENT'] = $post_value_assignment;
	$replacements['REQUEST_VALIDATION'] = $request_validation;
	$edit_method = new Zend_CodeGenerator_Php_Method();
	$edit_method_body = file_get_contents($crud_template_path . "/editAction.txt");
	$edit_method_body = RivetyCore_Common::replaceWithArray($edit_method_body, $replacements);
	$edit_method->setName('editAction')->setBody($edit_method_body);
	$controller_class->setMethod($edit_method);
	$edit_template = file_get_contents($crud_template_path . "/edit.tpl");
	$form_fields = "";
	$form_field_template = file_get_contents($crud_template_path . "/editTextInput.tpl");
	foreach ($columns as $colname => $column)
	{
		$field_string = $form_field_template;
		if (!in_array($colname, $reserved) && $colname != $id_column_name)
		{
			$input_replacements = array(
				"FIELD_LABEL"    => ucfirst($colname),
				"FIELD_NAME"     => $colname,
				"FIELD_VAR_NAME" => $entity_name_underscored . '.' . $colname,
			);
			$field_string = RivetyCore_Common::replaceWithArray($field_string, $input_replacements);
			$form_fields .= "\n" . $field_string . "\n";
		}
	}
	$replacements['FORM_FIELDS'] = $form_fields;
	$edit_template = RivetyCore_Common::replaceWithArray($edit_template, $replacements);

	// DELETE ACTION

	$delete_method = new Zend_CodeGenerator_Php_Method();
	$delete_method_body = file_get_contents($crud_template_path."/deleteAction.txt");
	$delete_method_body = RivetyCore_Common::replaceWithArray($delete_method_body, $replacements);
	$delete_method->setName('deleteAction')->setBody($delete_method_body);
	$controller_class->setMethod($delete_method);
	$delete_template = file_get_contents($crud_template_path . "/delete.tpl");
	$delete_template = RivetyCore_Common::replaceWithArray($delete_template, $replacements);

	// CONTROLLER CLASS

	$controller_file = new Zend_CodeGenerator_Php_File();
	$controller_file->setClass($controller_class);

	// RENDER FILES

	if ($dry_run)
	{
		die("\nDry run finished.\n");
	}
	else
	{
		file_write($theme_dir . "/index.tpl", $index_template);
		file_write($theme_dir . "/preview.tpl", $preview_template);
		file_write($theme_dir . "/edit.tpl", $edit_template);
		file_write($theme_dir . "/delete.tpl", $delete_template);
		file_write($output_basedir . "/modules/" . $module_name . "/models/" . $model_class_name . ".php", $model_file->generate());
		file_write($output_basedir . "/modules/" . $module_name . "/controllers/" . ucfirst($entity_name_oneword) . ($is_admin ? "admin" : "") . "Controller.php", $controller_file->generate());
		die("\nFinished.\n");
	}

}
