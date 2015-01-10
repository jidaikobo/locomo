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

// Autoloader::register()
Autoloader::register();
Autoloader::add_namespace('\\Locomo', LOCOMOPATH.'classes'.DS);
Autoloader::add_core_namespace('\\Locomo');

$paths = array(
/*
	'\\Locomo\\Actionset_Traits_Option_Testdata' => 'actionset/traits/option/testdata.php',
	'\\Locomo\\Actionset_Base' => 'actionset/base.php',
	'\\Locomo\\Actionset_Index' => 'actionset/index.php',
	'\\Locomo\\Actionset_Option' => 'actionset/option.php',
	'\\Locomo\\Controller_Traits_Testdata' => 'controller/traits/testdata.php',
	'\\Locomo\\Controller_Base' => 'controller/base.php',
	'\\Locomo\\Observer_Created' => 'observer/created.php',
	'\\Locomo\\Observer_Expired' => 'observer/expired.php',
	'\\Locomo\\Observer_Userids' => 'observer/userids.php',
	'\\Locomo\\Bulk' => 'bulk.php',
*/

	// these models are called by \Auth::is_root() at \Inflector::dir_to_ctrl() 
	'\\Locomo\\Model_Base' => 'model/base.php',
	'\\Locomo\\Model_Usr' => 'model/usr.php',
	'\\Locomo\\Model_Usrgrp' => 'model/usrgrp.php',
	'\\Locomo\\Model_Acl' => 'model/acl.php',

	// some app uses this
	'\\Locomo\\Model_Revision' => 'model/revision.php',

	// basic classes
	'\\Locomo\\Asset_Instance' => 'asset/instance.php',
	'\\Locomo\\Auth_Acl_Locomoacl' => 'auth/acl/locomoacl.php',
	'\\Locomo\\Auth_Group_Locomogroup' => 'auth/group/locomogroup.php',
	'\\Locomo\\Auth_Login_Locomoauth' => 'auth/login/locomoauth.php',
	'\\Locomo\\Fieldset_Field' => 'fieldset/field.php',
	'\\Locomo\\Actionset' => 'actionset.php',
	'\\Locomo\\Auth' => 'auth.php',
	'\\Locomo\\Fieldset' => 'fieldset.php',
	'\\Locomo\\Inflector' => 'inflector.php',
	'\\Locomo\\Module' => 'module.php',
	'\\Locomo\\Pagination' => 'pagination.php',
	'\\Locomo\\Request' => 'request.php',
	'\\Locomo\\Util' => 'util.php',
	'\\Locomo\\Validation' => 'validation.php',
);
$classes = array();
foreach ($paths as $class => $path)
{
	if ( ! file_exists(APPPATH.'classes/'.$path))
	{
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

// always load package
\Package::load('auth');

// add asset path
\Asset::add_path(LOCOMOPATH.'assets/');
\Asset::add_path(APPPATH.'locomo/assets/');

/* End of file bootstrap.php */
