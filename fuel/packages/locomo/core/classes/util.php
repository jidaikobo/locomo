<?php
namespace Locomo_Core;
class Util
{
	/**
	 * fetch_tpl()
	 */
	public static function fetch_tpl($path = null)
	{
		is_null($path) and \Response::redirect(\Uri::base());

		$tpl_path         = PKGPROJPATH.'modules/'.$path;
		$tpl_path_default = PKGCOREPATH.'modules/'.$path;

		if(file_exists($tpl_path)):
			return $tpl_path;
		elseif(file_exists($tpl_path_default)):
			return $tpl_path_default;
		else:
			return false;
		endif;
	}
	
	/**
	 * get_valid_actionset_name()
	 */
	public static function get_valid_actionset_name($module = null)
	{
		is_null($module) and \Response::redirect(\Uri::base());
		$module = ucfirst($module);
		return "\\$module\Actionset_".$module;
	}

	/**
	 * get_valid_controller_name()
	 */
	public static function get_valid_controller_name($controller = null)
	{
		is_null($controller) and \Response::redirect(\Uri::base());
		$controller_ucfirst = ucfirst($controller);
		return "\\$controller_ucfirst\Controller_".$controller_ucfirst;
	}

	/**
	 * get_valid_model_name()
	 */
	public static function get_valid_model_name($controller = null)
	{
		is_null($controller) and \Response::redirect(\Uri::base());
		$controller_ucfirst = ucfirst($controller);
		return "\\$controller_ucfirst\Model_".$controller_ucfirst;
	}

	/**
	 * get_module_name_from_class()
	 */
	public static function get_module_name_from_class($class = null)
	{
		is_null($class) and \Response::redirect(\Uri::base());
		$class = strtolower($class);
		$strs = explode('\\', $class);
		if(count($strs) > 2){
			return $strs[1];
		}else{
			return $strs[0];
		}
	}
}
