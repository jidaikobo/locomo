<?php

/*
 * Locomo - Accessible Web System Package for FuelPHP
 * bootstrap for locomo
 */

// version
define('LOCOMOVERSION', '1.8');

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
define('LOCOMOAPPPATH', APPPATH.'locomo'.DS);
define('LOCOMOUPLOADPATH', \Config::get('upload_path'));

// prepare locomo dir.
if ( ! file_exists(LOCOMOAPPPATH)) throw new \Exception("LOCOMOAPPPATH not found. create '".LOCOMOAPPPATH."'");

// Autoloader::register()
Autoloader::register();
Autoloader::add_namespace('Locomo', LOCOMOPATH.'classes'.DS);
Autoloader::add_core_namespace('Locomo');

// Autoloader::add_classes()
$targets = \Locomo\Util::get_file_list(LOCOMOPATH.'classes'.DS, 'file');
foreach ($targets as $path)
{
	$class = str_replace(array(LOCOMOPATH.'classes'.DS, '.php', DS), array('', '', '_'), $path);
	$app = str_replace(LOCOMOPATH, APPPATH, $path);
	if (file_exists($app))
	{
		$classes[$class] = $app;
	} else {
		$classes['Locomo\\'.$class] = $path;
	}
}
Autoloader::add_classes($classes);

// always load package
\Package::load('auth');

// add asset path
\Asset::add_path(LOCOMOPATH.'assets'.DS);
\Asset::add_path(APPPATH.'locomo'.DS.'assets'.DS);

/* End of file bootstrap.php */
