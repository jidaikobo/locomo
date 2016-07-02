<?php
namespace Locomo;
class Asset_Instance extends \Fuel\Core\Asset_Instance
{
	/*
	 * override render() to use locomo default assets
	 */
	public function render($group = null, $raw = false)
	{
		$retval = parent::render($group, $raw);
		return self::locomo_replace($retval);
	}

	/*
	 * override get_file() to use locomo default assets
	 */
	public function get_file($file, $type, $folder = '')
	{
		$retval = parent::get_file($file, $type, $folder);
		if ( ! $retval) return false;
		return self::locomo_replace($retval);
	}

	/*
	 * locomo_replace
	 */
	public function locomo_replace($retval)
	{
		// for windows env.
		$docroot = str_replace(DS, '/', DOCROOT);
		$apppath = str_replace(DS, '/', APPPATH);
		$locomopath = str_replace(DS, '/', LOCOMOPATH);

		// in case parent::render() fail to delete DOCROOT.
		$retval = str_replace($docroot, '', $retval);
		$apppath = str_replace($docroot, '', $apppath);
		$locomopath = str_replace($docroot, '', $locomopath);

		// app_assets を先にreplace
		if (strpos($retval, $apppath) !== false)
		{
			$replace = 'app_assets/';
			$search = $apppath.'locomo/assets/';
		}
		else
		{
			$replace = 'lcm_assets/';
			$search = $locomopath.'assets/';
		}

		return str_replace($search, $replace, $retval);
	}
}
