<?php

/*
 * Locomo - Accessible Web System Package for FuelPHP
 * bootstrap for locomo
 */

// version
define('LOCOMOVERSION', '1.1');

// load package
\Package::load('auth');

// PKGPATH
define('LOCOMOPATH', __DIR__.DS);

// add \Finder path
\Finder::instance()->add_path(LOCOMOPATH);

// load config
\Config::load('form', 'form');
if ( ! \Config::load('locomo'))
{
	\Config::load('packageconfig');
}

// path
define('LOCOMOAPPPATH', APPPATH.'locomo/');
define('LOCOMOUPLOADPATH', \Config::get('upload_path'));

// prepare locomo dir.
if ( ! file_exists(LOCOMOAPPPATH)) throw new \Exception("LOCOMOAPPPATH not found. create '".LOCOMOAPPPATH."'");

// Autoloader::register()
Autoloader::register();
Autoloader::add_namespace('Locomo', LOCOMOPATH.'classes'.DS);
Autoloader::add_core_namespace('Locomo');

// Autoloader::add_classes()
$targets = \Locomo\Util::get_file_list(LOCOMOPATH.'classes/', 'file');
foreach ($targets as $path)
{
	$class = str_replace(array(LOCOMOPATH.'classes/', '.php', '/'), array('', '', '_'), $path);
	$app = str_replace(LOCOMOPATH, APPPATH, $path);
	if (file_exists($app))
	{
		$classes[$class] = $app;
	} else {
		$classes['Locomo\\'.$class] = $path;
	}
}
Autoloader::add_classes($classes);

/*

$paths = array(
	// these models are called by \Auth::is_root() at \Inflector::dir_to_ctrl() 
	'\\Locomo\\Model_Auth_Usrgrps' => 'model/auth/usrgrps.php',
	'\\Locomo\\Model_Auth' => 'model/auth.php',
	'\\Locomo\\Model_Acl' => 'model/acl.php',

	// basic classes
	'\\Locomo\\Inflector' => 'inflector.php',
	'\\Locomo\\Util' => 'util.php',
	'\\Locomo\\Auth' => 'auth.php',
	'\\Locomo\\Auth_Acl_Locomoacl' => 'auth/acl/locomoacl.php',
	'\\Locomo\\Auth_Group_Locomogroup' => 'auth/group/locomogroup.php',
	'\\Locomo\\Auth_Login_Locomoauth' => 'auth/login/locomoauth.php',
	'\\Locomo\\Pagination' => 'pagination.php',
	'\\Locomo\\Actionset' => 'actionset.php',
	'\\Locomo\\Module' => 'module.php',
	'\\Locomo\\Request' => 'request.php',
//	'\\Locomo\\Asset_Instance' => 'asset/instance.php',
//	'\\Locomo\\Fieldset_Field' => 'fieldset/field.php',
//	'\\Locomo\\Fieldset' => 'fieldset.php',
//	'\\Locomo\\Security' => 'security.php',
//	'\\Locomo\\Validation' => 'validation.php',
);
$classes = array();
foreach ($paths as $class => $path)
{
	if ( file_exists(APPPATH.'classes/'.$path))
	{
		Autoloader::load($class);
	} else {
		$classes[$class] = LOCOMOPATH.'classes/'.$path;
	}
}
Autoloader::add_classes($classes);

// other classes - this must be called after first Autoloader::add_classes()
$target = \Inflector::dir_to_ctrl(LOCOMOPATH.'classes/');
foreach ($target as $class => $path)
{
	$app = str_replace(LOCOMOPATH, APPPATH, $path);
	if ( ! file_exists($app))
	{
		$classes['\\Locomo'.$class] = $path;
	}
}
Autoloader::add_classes($classes);
*/

// always load package
\Package::load('auth');

// add asset path
\Asset::add_path(LOCOMOPATH.'assets/');
\Asset::add_path(APPPATH.'locomo/assets/');

/* End of file bootstrap.php */
