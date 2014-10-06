<?php
/*
 *オーバライドがあったら優先してAutoloaderに加える仕様のbootstrap
 */

//project.iniを確認
$ini_path = dirname(dirname(__DIR__)).DS.'projects/projects.ini';
if( ! file_exists($ini_path)) die('projects.ini is missing');
$projects = parse_ini_file($ini_path, true);

//HTTP_HOSTを確認
if( ! (bool) defined('STDIN')):
	$host = \Input::server('HTTP_HOST');
	if( ! isset($projects['hosts'][$host])):
		die();
	endif;
else:
	$host = $projects['hosts']['cli_host'];
endif;

//PKGPATH
define('PKGCOREPATH', dirname(__DIR__).DS);
define('PKGPROJPATH', dirname(dirname(__DIR__)).DS.'projects/'.$projects['hosts'][$host].DS);
define('PROJECTDIR', $projects['hosts'][$host]);
define('PROJECTVIEWDIR', $projects['view'][$host]);

//Autoloader::register()
Autoloader::register();

//Autoloader - add_core_namespace 'Kontiki_Core'
//Kontiki_Coreは、ルート名前空間として登録する。
Autoloader::add_core_namespace('Kontiki_Core', PKGCOREPATH.'classes');

//coreのclassを走査
foreach (glob(PKGCOREPATH."classes".DS."*") as $filename):
	//class names and pathes
	$class = substr(\Inflector::words_to_upper(basename($filename)), 0, -4);//remove .php
	$l_class = strtolower($class);

	//Kontiki_Core名前空間の登録
	Autoloader::add_class("Kontiki_Core\\{$class}", PKGCOREPATH."classes/{$l_class}.php");
	if(file_exists(PKGPROJPATH."classes/{$l_class}.php")):
		//プロジェクト側にオーバライドがあったらKontiki名前空間として登録
		Autoloader::add_class("Kontiki\\{$class}", PKGPROJPATH."classes/{$l_class}.php");
	else:
		//プロジェクト側にオーバライドがなければ、Kontiki名前空間をKontiki_Core名前空間と見なす
		Autoloader::alias_to_namespace("Kontiki_Core\\{$class}", 'Kontiki');
	endif;
endforeach;

//Autoloader - base modules
$module_names = array();
$mvcs = array('controller','model','view',);
foreach (glob(PKGCOREPATH."modules".DS."*") as $dirname):
	//module names and pathes
	$module = ucfirst(basename($dirname));
	$l_module = strtolower($module);
	$coremodpath = PKGCOREPATH."modules/{$l_module}/classes/";
	$projmodpath = PKGPROJPATH."modules/{$l_module}/classes/";

	//MVCディレクトリを走査
	foreach($mvcs as $mvc):
		if(file_exists($coremodpath."{$mvc}/{$l_module}.php")):
			$umvc = ucfirst($mvc);
			//まず、Kontiki_Core_Moduleの名前空間で登録しておく
			Autoloader::add_class(
				"Kontiki_Core_Module\\{$module}\\{$umvc}_{$module}",
				$coremodpath."{$mvc}/{$l_module}.php"
			);
			//コアモジュールをオーバライドするプロジェクトモジュールがなければ、Kontiki_Core_Moduleをデフォルトのモジュール名前空間と見なす
			if( ! file_exists($projmodpath."{$mvc}/{$l_module}.php")):
				Autoloader::alias_to_namespace(
					"Kontiki_Core_Module\\{$module}\\{$umvc}_{$module}",
					"{$module}"
				);
			endif;
		endif;
	endforeach;

	//trait
	$trait_path = $dirname.'/traits/';
	foreach (glob($trait_path."*") as $filename):
		Fuel::load($filename);
	endforeach;
endforeach;

//Autoloader - observer
$observer_class_names = array();
foreach (glob(PKGCOREPATH."observers".DS."*") as $filename):
	//class names and pathes
	$class = substr(\Inflector::words_to_upper(basename($filename)), 0, -4);//remove .php
	$l_class = strtolower($class);
	$default_path  = PKGCOREPATH."observers/{$l_class}.php";
	$override_path = PKGPROJPATH."observers/{$l_class}.php";

	//setting
	$observer_class_names["Kontiki\\Observer\\{$class}"] = file_exists($override_path) ? $override_path : $default_path;
endforeach;
Autoloader::add_classes($observer_class_names);

// load the package with the config file.
if(file_exists(PKGPROJPATH.'config/packageconfig.php')):
	Config::load(PKGPROJPATH.'config/packageconfig.php');
else:
	Config::load(PKGCOREPATH.'config/packageconfig.php');
endif;

//
Config::load(PKGCOREPATH.'config/form.php','form');

//always load module
Module::load('acl');
Module::load('user');
Module::load('revision');
Module::load('workflow');
Module::load('option');

/* End of file bootstrap.php */
