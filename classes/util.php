<?php
namespace Locomo;
class Util
{
	/**
	 * get_mod_or_ctrl()
	 * Locomo配下にある対象コントローラ／モジュールの取得
	 * controllerがlocomoメンバ変数を持っているときにLocomo配下と見なす
	 * \Locomo\View::base_assign()で毎回呼ぶのでcacheする
	 * get locomo related modules and controllers
	 * @return array()
	 */
	public static function get_mod_or_ctrl()
	{
		// static cache
		static $cache = array();
		if ( ! empty($cache)) return $cache;

		//cache
		try
		{
			//root not use cache
			if (\Auth::is_root()) throw new \CacheNotFoundException();
			return \Cache::get('locomo_mod_or_ctrl');
		}
		catch (\CacheNotFoundException $e)
		{
			// モジュールディレクトリを走査し、$locomoのメンバ変数を持っているコントローラを洗い出す
			$retvals = array();
			foreach(array_keys(\Module::get_exists()) as $module)
			{
				if ( ! $controllers = \Module::get_controllers($module)) continue;// module without controllers
				\Module::loaded($module) or \Module::load($module);

				foreach($controllers as $controller)
				{
					$mod_ctrl = \Inflector::path_to_ctrl($controller);

					if ( ! property_exists($mod_ctrl, 'locomo')) continue;

					$config = \Config::load($module.'::'.$module, 'util', true, true);

					if( ! is_array($config)) continue;
					if( ! $main_controller = \Arr::get($config, 'main_controller')) continue;
					if (array_key_exists($main_controller, $retvals)) continue; // already exists

					$retvals[$main_controller] = $mod_ctrl::$locomo ; 
					$retvals[$main_controller]['config'] = $config;
					$retvals[$main_controller]['is_module'] = true;
				}
			}

			// classディレクトリを走査し、$locomoのメンバ変数を持っているコントローラを洗い出す
			$paths = array_merge(
				\Inflector::dir_to_ctrl(APPPATH.'classes/controller'),
				\Inflector::dir_to_ctrl(LOCOMOPATH.'classes/controller')
			);

			foreach(array_keys($paths) as $ctrl):
				if (strpos($ctrl, 'Controller_Traits_') !== false) continue;
				if ( ! property_exists($ctrl, 'locomo')) continue;
				$retvals[$ctrl] = $ctrl::$locomo;
				$retvals[$ctrl]['config'] = \Config::get($ctrl);
			endforeach;
	
			// order
			$retvals = \Arr::multisort($retvals, array('order_at_menu' => SORT_ASC,));

			// static cache
			$cache = $retvals;

			// cache 1 hour
			\Cache::set('locomo_mod_or_ctrl', $retvals, 3600);

			return $retvals;
		}
	}

	/**
	 * get_latestprefix()
	 * @param string dir
	 * @param string format
	 * @return string|false
	 */
	public static function get_latestprefix($dir, $format = '%03d')
	{
		$files = \File::read_dir($dir);
		sort($files);
		$latest_one = array_pop($files);
		$latest_prefix = intval(substr($latest_one, 0, strpos($latest_one, '_')));
		$latest_prefix = sprintf($format , $latest_prefix + 1);
		return $latest_prefix;
	}


	/*
	 * 年度の期間を between 句で返す
	 * todo 4月のみ対応しているので、後々変える
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
