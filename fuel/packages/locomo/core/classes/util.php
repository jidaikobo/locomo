<?php
namespace Locomo;
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

	/*
	 * @return where between 句
	 */
	public static function get_fiscal($date = null) {

		
		if (preg_match('/^([0-9]{4})$/', $date)) $date = date('Y-n-d', strtotime($date . '-04-01'));
		//var_dump(date('Y-n', strtotime($date))); die();
		!$date and $date = date('Y-m-d');

		if (date('n', strtotime($date)) < 4) {
			$year = date('Y', strtotime($date)) - 1;
		} else {
			$year = date('Y', strtotime($date));
		}

		return \DB::expr('"' . $year . '-04-01 00:00:00"' . ' and ' . '"' . ($year+1) . '-03-31 23:59:59"');
	}
}
