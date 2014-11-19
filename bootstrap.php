<?php
/*
 * Locomo - Accessible Web System Package for FuelPHP
 * bootstrap for locomo
 */

// load package
\Package::load('auth');

// PKGPATH
define('LOCOMOPATH', __DIR__.DS);

// add \Finder path
\Finder::instance()->add_path(LOCOMOPATH);

// load config
\Config::load('form', 'form');
if( ! \Config::load('locomo'))
{
	\Config::load('packageconfig');
}

// Autoloader::register()
Autoloader::register();
Autoloader::add_namespace('\\Locomo', LOCOMOPATH.'classes'.DS);
Autoloader::add_core_namespace('\\Locomo');

// add to core namespace
Autoloader::add_classes(array(
	'\\Locomo\\Actionset'              => LOCOMOPATH.'classes'.DS.'actionset.php',
	'\\Locomo\\Actionset_Base'         => LOCOMOPATH.'classes'.DS.'actionset/base.php',
	'\\Locomo\\Actionset_Owner'        => LOCOMOPATH.'classes'.DS.'actionset/owner.php',
	'\\Locomo\\Actionset_Option'       => LOCOMOPATH.'classes'.DS.'actionset/option.php',
	'\\Locomo\\Actionset_Index'        => LOCOMOPATH.'classes'.DS.'actionset/index.php',
	'\\Locomo\\Util'                   => LOCOMOPATH.'classes'.DS.'util.php',
	'\\Locomo\\Auth'                   => LOCOMOPATH.'classes'.DS.'auth.php',//package
	'\\Locomo\\Auth_Login_Locomoauth'  => LOCOMOPATH.'classes'.DS.'auth/login/locomoauth.php',
	'\\Locomo\\Auth_Group_Locomogroup' => LOCOMOPATH.'classes'.DS.'auth/group/locomogroup.php',
	'\\Locomo\\Auth_Acl_Locomoacl'     => LOCOMOPATH.'classes'.DS.'auth/acl/locomoacl.php',
));

// core override class
Autoloader::add_classes(array(
	'\\Locomo\\Asset_Instance' => LOCOMOPATH.'classes'.DS.'asset/instance.php',
	'\\Locomo\\Validation'     => LOCOMOPATH.'classes'.DS.'validation.php',
	'\\Locomo\\Pagination'     => LOCOMOPATH.'classes'.DS.'pagination.php',
	'\\Locomo\\Fieldset'       => LOCOMOPATH.'classes'.DS.'fieldset.php',
	'\\Locomo\\Fieldset_Field' => LOCOMOPATH.'classes'.DS.'fieldset/field.php',
	'\\Locomo\\Module'         => LOCOMOPATH.'classes'.DS.'module.php',
	'\\Locomo\\Request'        => LOCOMOPATH.'classes'.DS.'request.php',
	'\\Locomo\\View'           => LOCOMOPATH.'classes'.DS.'view.php',
	'\\Locomo\\Inflector'      => LOCOMOPATH.'classes'.DS.'inflector.php',
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
\Asset::add_path(LOCOMOPATH.'view/');
\Asset::add_path(DOCROOT.'view/');

/* End of file bootstrap.php */
