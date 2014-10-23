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
	$host = isset($_SERVER['LOCOMO_ENV']) ?
		$_SERVER['LOCOMO_ENV'] :
		$projects['hosts']['cli_host'];
endif;

//PKGPATH
define('PKGCOREPATH', dirname(__DIR__).DS);
define('PKGPROJPATH', dirname(dirname(__DIR__)).DS.'projects/'.$projects['hosts'][$host].DS);
define('PROJECTDIR', $projects['hosts'][$host]);
define('PROJECTVIEWDIR', $projects['view'][$host]);

//検索パスの追加
\Finder::instance()->add_path(PKGCOREPATH);
\Finder::instance()->add_path(PKGPROJPATH);

//Autoloader::register()
Autoloader::register();

//Autoloader - add_core_namespace 'Locomo_Core'
//Locomo_Coreは、ルート名前空間として登録する。
Autoloader::add_core_namespace('Locomo_Core', PKGCOREPATH.'classes');

//class走査用のクロージャ
$func_get_classname = function($filename){
		return substr(\Inflector::words_to_upper(basename($filename)), 0, -4);
	};

//coreのclassを走査
$classes = array();
foreach (glob(PKGCOREPATH."classes".DS."*") as $filename):
	//is_dir()
	if(is_dir($filename)):
		//follow fuel's way - relate class must be in same name dir
		foreach (glob($filename.DS."*") as $child_filename):
			$child_dirname = basename(dirname($child_filename));
			$child_filename = basename($child_filename);
			$path_part = $child_dirname.'/'.$child_filename;
			$class = $func_get_classname($child_dirname.'_'.$child_filename);
			$classes[$path_part] = $class;
		endforeach;
	//file
	else:
		$path_part = basename($filename);
		$class = $func_get_classname($path_part);
		$classes[$path_part] = $class;
	endif;
endforeach;
foreach($classes as $path_part => $class):
	//Locomo_Core名前空間の登録
	Autoloader::add_class("Locomo_Core\\{$class}", PKGCOREPATH."classes/{$path_part}");
	if(file_exists(PKGPROJPATH."classes/{$path_part}")):
		//プロジェクト側にオーバライドがあったらLocomo名前空間として登録
		Autoloader::add_class("Locomo\\{$class}", PKGPROJPATH."classes/{$path_part}");
	else:
		//プロジェクト側にオーバライドがなければ、Locomo名前空間をLocomo_Core名前空間と見なす
		Autoloader::alias_to_namespace("Locomo_Core\\{$class}", 'Locomo');
	endif;
endforeach;

//Autoloader - base modules
$module_names = array();
$mvcs = array('controller','model','view','actionset',);
foreach (glob(PKGCOREPATH."modules".DS."*") as $dirname):
	//module names and pathes
	$module = ucfirst(basename($dirname));
	$l_module = strtolower($module);
	$coremodpath = PKGCOREPATH."modules/{$l_module}/classes/";
	$projmodpath = PKGPROJPATH."modules/{$l_module}/classes/";

	//MVCディレクトリを走査
	foreach($mvcs as $mvc):
		foreach(glob($coremodpath."{$mvc}".DS."*") as $each_path):
			$classname = ucfirst(substr(basename($each_path), 0, -4));
			$l_classname = strtolower($classname);
			$umvc = ucfirst($mvc);
			//まず、Locomo_Core_Moduleの名前空間で登録しておく
			Autoloader::add_class(
				"Locomo_Core_Module\\{$module}\\{$umvc}_{$classname}",
				$coremodpath."{$mvc}/{$l_classname}.php"
			);
			//コアモジュールをオーバライドするプロジェクトモジュールがなければ、Locomo_Core_Moduleをデフォルトのモジュール名前空間と見なす
			if( ! file_exists($projmodpath."{$mvc}/{$classname}.php")):
				Autoloader::alias_to_namespace(
					"Locomo_Core_Module\\{$module}\\{$umvc}_{$classname}",
					"{$module}"
				);
			endif;
		endforeach;
	endforeach;

	//trait
	$trait_path = $dirname.'/traits/';
	foreach (glob($trait_path."*") as $dirpath):
		foreach (glob($dirpath.DS."*") as $filepath):
			\Fuel::load($filepath);
		endforeach;
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
	$observer_class_names["Locomo\\Observer\\{$class}"] = file_exists($override_path) ? $override_path : $default_path;
endforeach;
Autoloader::add_classes($observer_class_names);

// load the package with the config file.
$paths = array(PKGCOREPATH,PKGPROJPATH);
$finder = \Finder::forge($paths);
foreach($finder->list_files('config') as $path):
	$filename = basename($path);
	$name = substr(strtolower(basename($filename)), 0, -4);//remove .php
	$name = $filename == 'packageconfig.php' ? '' : $name ;
	if($name):
		Config::load($path, $name);
	else:
		Config::load($path);
	endif;
endforeach;

//always load module
Module::load('acl');
Module::load('user');
Module::load('revision');
Module::load('workflow');
Module::load('option');

/* End of file bootstrap.php */
