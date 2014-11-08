<?php
/*
 * bootstrap for locomo
 */

// get project.ini
$ini_path = dirname(dirname(__DIR__)).DS.'projects/projects.ini';
if( ! file_exists($ini_path)) die('projects.ini is missing');
$projects = parse_ini_file($ini_path, true);

// check HTTP_HOST
if( ! (bool) defined('STDIN')):
	$host = \Input::server('HTTP_HOST');
	if( ! isset($projects['hosts'][$host])):
		die();
	endif;
else:
	// define cli host for oil command
	$host = isset($_SERVER['LOCOMO_ENV']) ?
		$_SERVER['LOCOMO_ENV'] :
		$projects['hosts']['cli_host'];
endif;

// PKGPATH
define('PKGCOREPATH', dirname(__DIR__).DS);
define('PKGPROJPATH', dirname(dirname(__DIR__)).DS.'projects/'.$projects['hosts'][$host].DS);
define('PROJECTDIR', $projects['hosts'][$host]);
define('PROJECTVIEWDIR', $projects['view'][$host]);

// add \Finder path
\Finder::instance()->add_path(PKGCOREPATH);
\Finder::instance()->add_path(PKGPROJPATH);

// load config
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

// Autoloader::register()
Autoloader::register();
Autoloader::add_namespace('Locomo', PKGCOREPATH.'classes'.DS);
Autoloader::add_core_namespace('Locomo');

// add to core namespace
Autoloader::add_classes(array(
	'Locomo\\Actionset'              => PKGCOREPATH.'classes'.DS.'actionset.php',
	'Locomo\\Actionset_Base'         => PKGCOREPATH.'classes'.DS.'actionset/base.php',
	'Locomo\\Actionset_Owner'        => PKGCOREPATH.'classes'.DS.'actionset/owner.php',
	'Locomo\\Actionset_Option'       => PKGCOREPATH.'classes'.DS.'actionset/option.php',
	'Locomo\\Actionset_Index'        => PKGCOREPATH.'classes'.DS.'actionset/index.php',
	'Locomo\\Util'                   => PKGCOREPATH.'classes'.DS.'util.php',
	'Locomo\\Auth'                   => PKGCOREPATH.'classes'.DS.'auth.php',
	'Locomo\\Auth_Login_Locomoauth'  => PKGCOREPATH.'classes'.DS.'auth/login/locomoauth.php',
	'Locomo\\Auth_Group_Locomogroup' => PKGCOREPATH.'classes'.DS.'auth/group/locomogroup.php',
	'Locomo\\Auth_Acl_Locomoacl'     => PKGCOREPATH.'classes'.DS.'auth/acl/locomoacl.php',
	'Locomo\\View'                   => PKGCOREPATH.'classes'.DS.'view.php',
));

// core override class
Autoloader::add_classes(array(
	'Locomo\\Asset_Instance' => PKGCOREPATH.'classes'.DS.'asset/instance.php',
	'Locomo\\Validation'     => PKGCOREPATH.'classes'.DS.'validation.php',
	'Locomo\\Pagination'     => PKGCOREPATH.'classes'.DS.'pagination.php',
	'Locomo\\Fieldset'       => PKGCOREPATH.'classes'.DS.'fieldset.php',
	'Locomo\\Fieldset_Field' => PKGCOREPATH.'classes'.DS.'fieldset/field.php',
	'Locomo\\Module'         => PKGCOREPATH.'classes'.DS.'module.php',
	'Locomo\\Request'        => PKGCOREPATH.'classes'.DS.'request.php',
));

// always load module
\Module::load('acl');
\Module::load('user');
\Module::load('revision');
\Module::load('workflow');
\Module::load('bulk');

// always load package
\Package::load('auth');

// add asset path
\Asset::add_path(PKGCOREPATH.'view/');
\Asset::add_path(DOCROOT.'view/');

/* End of file bootstrap.php */
