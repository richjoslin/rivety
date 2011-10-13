#!/usr/bin/php
<?php

require_once('cli_header.php');

if (!array_key_exists("1", $argv)) {
	die("Usage: makemodule.php [modulename]\n");
} else {
	$module_name = $argv[1];
	$module_name_uc = ucfirst($module_name);
}

if (!preg_match('/^\p{Ll}*$/', $module_name)) {
	die("Module name must only contain lowercase letters and nothing else.");
}

echo("Module Name: ".$module_name."\n");

$template_dir = $basepath."/modules/default/extras/makemodule_templates";
$module_dir = $basepath."/modules/".$module_name;
$frontend_theme_dir = $basepath."/themes/frontend/default/modules/".$module_name;
$admin_theme_dir = $basepath."/themes/admin/default/modules/".$module_name;

if (is_dir($module_dir)) {
	die("This module already exists! I'm not just gonna overwrite it, all willy-nilly.");
}

// COMPLETE FOLDER STRUCTURE
mkdir($module_dir, 0777, true);
mkdir($module_dir."/controllers", 0777, true);
mkdir($module_dir."/lib", 0777, true);
mkdir($module_dir."/lib/".$module_name_uc."/Db", 0777, true);
mkdir($module_dir."/lib/".$module_name_uc."/Plugins", 0777, true);
mkdir($module_dir."/models", 0777, true);
mkdir($module_dir."/sql", 0777, true);
mkdir($module_dir."/sql/PDO_MYSQL", 0777, true);
mkdir($frontend_theme_dir."/index", 0777, true);
mkdir($admin_theme_dir."/admin", 0777, true);

// MODULE.INI
$ini_template = file_get_contents($template_dir."/module.ini.txt");
$ini_template = str_replace(array("MODULE_NAME_LOWER", "MODULE_NAME_UC"), array($module_name, $module_name_uc), $ini_template);
file_put_contents($module_dir."/module.ini", $ini_template);

// INIT METHOD (required in all Communitas controller classes)
$init_method = new Zend_CodeGenerator_Php_Method();
$init_method->setName('init')->setBody("\t\tparent::init();");

// FRONTEND INDEX
$index_smarty_template = file_get_contents($template_dir."/index.tpl.txt");
$index_smarty_template = str_replace("MODULE_NAME_UC", $module_name_uc, $index_smarty_template);
file_put_contents($frontend_theme_dir."/index/index.tpl", $index_smarty_template);
$index_controller_template = file_get_contents($template_dir."/IndexController.php.txt");
$index_controller_template = str_replace("MODULE_NAME_UC", $module_name_uc, $index_controller_template);
file_put_contents($module_dir."/controllers/IndexController.php", $index_controller_template);

// ADMIN INDEX
$admin_index_smarty_template = file_get_contents($template_dir."/index_admin.tpl.txt");
$admin_index_smarty_template = str_replace("MODULE_NAME", $module_name_uc, $admin_index_smarty_template);
file_put_contents($admin_theme_dir."/admin/index.tpl", $admin_index_smarty_template);
$admin_index_controller_template = file_get_contents($template_dir."/AdminController.php.txt");
$admin_index_controller_template = str_replace("MODULE_NAME_UC", $module_name_uc, $admin_index_controller_template);
file_put_contents($module_dir."/controllers/AdminController.php", $admin_index_controller_template);

// DB TABLE ABSTRACT
$table_abstract_template = file_get_contents($template_dir."/Table.php.txt");
$table_abstract_template = str_replace(array("MODULE_NAME_UC", "MODULE_NAME_LOWER"), array($module_name_uc, $module_name), $table_abstract_template);
file_put_contents($module_dir."/lib/".$module_name_uc."/Db/Table.php", $table_abstract_template);

// SETUP PLUGIN
$setup_plugin_template = file_get_contents($template_dir."/Setup.php.txt");
$setup_plugin_template = str_replace(array("MODULE_NAME_UC", "MODULE_NAME_LOWER"), array($module_name_uc, $module_name), $setup_plugin_template);
file_put_contents($module_dir."/lib/".$module_name_uc."/Plugins/Setup.php", $setup_plugin_template);

// BLANK STARTER FILES
file_put_contents($module_dir."/sql/PDO_MYSQL/install.sql", "");
file_put_contents($module_dir."/sql/PDO_MYSQL/testdata.sql", "");
file_put_contents($module_dir."/sql/PDO_MYSQL/uninstall.sql", "");

die("Done.");
