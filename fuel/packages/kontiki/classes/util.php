<?php
namespace Kontiki;
class Util
{
	/**
	 * fetch_tpl()
	 */
	public static function fetch_tpl($path = null)
	{
		is_null($path) and \Response::redirect(\Uri::base());

		$tpl_path         = PKGPATH.'kontiki/modules/'.$path;
		$tpl_path_default = PKGPATH.'kontiki/modules_default/'.$path;
		if(file_exists($tpl_path)):
			return $tpl_path;
		elseif(file_exists($tpl_path_default)):
			return $tpl_path_default;
		else:
			return false;
		endif;
	}

	/**
	 * get_controller_valid_name()
	 */
	public static function get_controller_valid_name($controller = null)
	{
		is_null($controller) and \Response::redirect(\Uri::base());
		$controller_ucfirst = ucfirst($controller);
		return "\\$controller_ucfirst\Controller_".$controller_ucfirst;
	}
}
