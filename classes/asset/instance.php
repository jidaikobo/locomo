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
		$replace = 'content/fetch_view/';

		if (strpos($retval, APPPATH) !== false)
		{
			$search = APPPATH.'locomo/assets';
		}
		else
		{
			//in case parent::render() fail to delete DOCROOT.
			$search = str_replace(DOCROOT, '', LOCOMOPATH).'assets/';
			$retval = str_replace(DOCROOT, '', $retval);
		}
		return str_replace($search, $replace, $retval);
	}
}