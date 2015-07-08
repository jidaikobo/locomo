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

		// app_assetx を先に replace
		if (strpos($retval, APPPATH) !== false)
		{
			$replace = 'app_assets/';
			$search = APPPATH.'locomo/assets/';
			$retval = str_replace($search, $replace, $retval);
		}

		$replace = 'lcm_assets/';
		// in case parent::render() fail to delete DOCROOT.
		$search = str_replace(DOCROOT, '', LOCOMOPATH).'assets/';
		$retval = str_replace(DOCROOT, '', $retval);

		return str_replace($search, $replace, $retval);
	}



}
