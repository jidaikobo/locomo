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

		if(strpos($retval, APPPATH) !== false)
		{
			$search = APPPATH.'locomo/assets';
			$replace = 'content/fetch_view';
		}
		else
		{
			$search = LOCOMOPATH.'assets';
			$replace = 'content/fetch_view';
		}
		return str_replace($search, $replace, $retval);
	}
}
