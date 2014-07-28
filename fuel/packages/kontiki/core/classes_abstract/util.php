<?php
namespace Kontiki;
abstract class Util_Abstract
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
	 * object_merge()
	 * thx http://d.hatena.ne.jp/rsky/20070808/1186578579
	 */
	public static function object_merge()
	{
		$args = func_get_args();
		if ( ! $args) {
			return null;
		}
		return (object) call_user_func_array('array_merge', array_map('get_object_vars', $args));
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
