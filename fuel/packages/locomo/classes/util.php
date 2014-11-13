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

		$tpl_path_default = LOCOMOPATH.'modules/'.$path;

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
	 * get_mod_or_ctrl()
	 * Locomo配下にある対象コントローラ／モジュールの取得
	 * controllerがlocomoメンバ変数を持っているときにLocomo配下と見なす
	 * \Locomo\View::base_assign()で毎回呼ぶのでcacheする
	 */
	public static function get_mod_or_ctrl()
	{
		//cache
		try
		{
			//root not use cache
			if(\Auth::is_root()) throw new \CacheNotFoundException();
			return \Cache::get('locomo_mod_or_ctrl');
		}
		catch (\CacheNotFoundException $e)
		{
			//モジュールディレクトリを走査し、$locomoのメンバ変数を持っている物を洗い出す
			$retvals = array();
			foreach(array_keys(\Module::get_exists()) as $module)
			{
				if( ! $controllers = \Module::get_controllers($module)) continue;// module without controllers
				\Module::loaded($module) or \Module::load($module);
				foreach($controllers as $controller)
				{
					$mod_ctrl = \Inflector::path_to_ctrl($controller);
					if( ! property_exists($mod_ctrl, 'locomo')) continue;
					if(array_key_exists($module, $retvals)) continue; // already exists
					$retvals[$module] = $mod_ctrl::$locomo ; 
				}
			}
	
			//classディレクトリを走査し、$locomoのメンバ変数を持っている物を洗い出す
			foreach(array_keys(\Inflector::dir_to_ctrl(APPPATH.'classes/controller')) as $ctrl):
				if( ! property_exists($ctrl, 'locomo')) continue;
				$retvals[$ctrl] = $ctrl::$locomo ; 
			endforeach;
	
			//順番制御
			$retvals = \Arr::multisort($retvals, array('order_at_menu' => SORT_ASC,));
	
			//cache 1 hour
			\Cache::set('locomo_mod_or_ctrl', $retvals, 3600);

			return $retvals;
		}
	}
}
