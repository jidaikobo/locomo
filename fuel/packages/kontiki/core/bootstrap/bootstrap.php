<?php
/*
 *オーバライドがあったら優先してAutoloaderに加える仕様のbootstrap
 */

//PKGPATH
define('PKGCOREPATH', dirname(__DIR__).DS);
define('PKGAPPPATH', dirname(dirname(__DIR__)).DS);

//Autoloader - kontiki
Autoloader::register();
Autoloader::add_namespace('Kontiki', PKGCOREPATH.'classes');

//Autoloader - classes
$class_names = array();
foreach (glob(PKGCOREPATH."classes_abstract".DS."*") as $filename):
	//class names and pathes
	$class = substr(\Inflector::words_to_upper(basename($filename)), 0, -4);//remove .php
	$l_class = strtolower($class);
	$abstract_path = PKGCOREPATH."classes_abstract/{$l_class}.php";
	$default_path  = PKGCOREPATH."classes/{$l_class}.php";
	$override_path = PKGAPPPATH."classes/{$l_class}.php";

	//abstract
	$class_names["Kontiki\\{$class}_Abstract"] = $abstract_path;

	//override
	$class_names["Kontiki\\{$class}"] = file_exists($override_path) ? $override_path : $default_path;
endforeach;
Autoloader::add_classes($class_names);

//Autoloader - base modules
$module_names = array();
foreach (glob(PKGCOREPATH."modules".DS."*") as $dirname):
	//module names and pathes
	$module = ucfirst(basename($dirname));
	$l_module = strtolower($module);
	$path = PKGCOREPATH."modules/{$l_module}/classes/";

	//setting
	$module_names["Kontiki\\Controller_{$module}"] = $path."controller/{$l_module}_abstract.php";
	$module_names["Kontiki\\Model_{$module}"]      = $path."model/{$l_module}_abstract.php";
	$module_names["Kontiki\\View_{$module}"]       = $path."view/{$l_module}_abstract.php";
endforeach;
Autoloader::add_classes($module_names);

//Autoloader - observer
$observer_class_names = array();
foreach (glob(PKGCOREPATH."observers".DS."*") as $filename):
	//class names and pathes
	$class = substr(\Inflector::words_to_upper(basename($filename)), 0, -4);//remove .php
	$l_class = strtolower($class);
	$default_path  = PKGCOREPATH."observers/{$l_class}.php";
	$override_path = PKGAPPPATH."observers/{$l_class}.php";

	//setting
	$observer_class_names["Kontiki\\Observer\\{$class}"] = file_exists($override_path) ? $override_path : $default_path;
endforeach;
Autoloader::add_classes($observer_class_names);

// load the package with the config file.
if(file_exists(PKGPATH.'kontiki/config/packageconfig.php')):
	Config::load('packageconfig.php');
else:
	Config::load(PKGCOREPATH.'/config/packageconfig.php');
endif;

//always load module
\Module::load('acl');
\Module::load('user');
\Module::load('usergroup');
\Module::load('revision');
\Module::load('workflow');

/* End of file bootstrap.php */
