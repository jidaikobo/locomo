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

//add \Finder path
\Finder::instance()->add_path(PKGCOREPATH);
\Finder::instance()->add_path(PKGPROJPATH);

//load config
$finder = \Finder::forge(array(PKGPROJPATH, PKGCOREPATH));
$loaded = array();
foreach($finder->list_files('config') as $path):
	$filename = basename($path);
	if(in_array($filename, $loaded)) continue;
	$name = substr(strtolower(basename($filename)), 0, -4);//remove .php
	$name = $filename == 'packageconfig.php' ? null : $name ;
	\Config::load($path, $name);
	$loaded[] = $filename;
endforeach;

//Autoloader::register()
Autoloader::register();
Autoloader::add_namespace('Locomo', PKGCOREPATH.'classes'.DS);
Autoloader::add_core_namespace('Locomo');
Autoloader::add_core_namespace('Locomo_Module');

//add to core namespace
Autoloader::add_classes(array(
	'Locomo\\Actionset'       => PKGCOREPATH.DS.'classes'.DS.'actionset.php',
	'Locomo\\Actionset_Base'  => PKGCOREPATH.DS.'classes'.DS.'actionset/base.php',
	'Locomo\\Actionset_Owner' => PKGCOREPATH.DS.'classes'.DS.'actionset/owner.php',
	'Locomo\\Actionset_Index' => PKGCOREPATH.DS.'classes'.DS.'actionset/index.php',
	'Locomo\\Util'            => PKGCOREPATH.DS.'classes'.DS.'util.php',
));

//core override class
Autoloader::add_classes(array(
	'Locomo\\Validation' => PKGCOREPATH.DS.'classes'.DS.'validation.php',
	'Locomo\\Pagination' => PKGCOREPATH.DS.'classes'.DS.'pagination.php',
	'Locomo\\Fieldset'   => PKGCOREPATH.DS.'classes'.DS.'fieldset.php',
	'Locomo\\Module'     => PKGCOREPATH.DS.'classes'.DS.'module.php',
));

//always load module
Module::load('acl');
Module::load('user');
Module::load('revision');
Module::load('workflow');
Module::load('option');

/* End of file bootstrap.php */
