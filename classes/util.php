<?php
namespace Locomo;

class SortedIterator extends \SplHeap
{
//	public function __construct(Iterator $iterator)
	public function __construct($iterator)
	{
		foreach ($iterator as $item) {
			$this->insert($item);
		}
	}
	public function compare($b,$a)
	{
		return strcmp($a->getRealpath(), $b->getRealpath());
	}
}

class Util
{
	/**
	 * get_mod_or_ctrl()
	 * Locomo配下にある対象コントローラ／モジュールの取得
	 * controllerがlocomoメンバ変数を持っているときにLocomo配下と見なす
	 * \Locomo\Presenter_Header::view()で毎回呼ぶのでcacheする
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
			// モジュールディレクトリを走査し、メインコントローラを探す
			$retvals = array();
			foreach(array_keys(\Module::get_exists()) as $module)
			{
				if ( ! $controllers = \Module::get_controllers($module)) continue;// module without controllers
				\Module::loaded($module) or \Module::load($module);

				// load config
				$config = \Config::load($module.'::'.$module, 'util', true, true);
				if( ! is_array($config)) continue;
				if( ! $ctrl = \Arr::get($config, 'main_controller')) continue; // check main controller
				if ( ! property_exists($ctrl, 'locomo')) continue; // main controller has no $locomo
				if (array_key_exists($ctrl, $retvals)) continue; // already exists

				// update retvals
				$retvals[$ctrl]['is_module'] = true;
				$retvals[$ctrl]['nicename'] = \Arr::get($config, 'nicename', $module);
				$retvals[$ctrl]['explanation'] = \Arr::get($config, 'explanation', '') ;
				$main_action = \Arr::get($ctrl::$locomo, 'main_action', '');
				$retvals[$ctrl]['main_action'] = $main_action ? $ctrl.DS.$main_action : '' ;
				$retvals[$ctrl]['show_at_menu'] = \Arr::get($config, 'show_at_menu', true) ;
				$retvals[$ctrl]['is_for_admin'] = \Arr::get($config, 'is_for_admin', false) ;
				$retvals[$ctrl]['no_acl'] = \Arr::get($config, 'no_acl', false) ;
				$retvals[$ctrl]['widgets'] = \Arr::get($config, 'widgets') ;
				$retvals[$ctrl]['order'] = \Arr::get($config, 'order', 100) ;
			}

			// classディレクトリを走査し、$locomoのメンバ変数を持っているコントローラを洗い出す
			$paths = array_merge(
				\Inflector::dir_to_ctrl(APPPATH.'classes'.DS.'controller'),
				\Inflector::dir_to_ctrl(LOCOMOPATH.'classes'.DS.'controller')
			);

			foreach(array_keys($paths) as $ctrl)
			{
				if (strpos($ctrl, 'Controller_Traits_') !== false) continue;
				if ( ! property_exists($ctrl, 'locomo')) continue;
				$retvals[$ctrl]['is_module'] = false;
				$retvals[$ctrl]['nicename'] = \Arr::get($ctrl::$locomo, 'nicename', $ctrl);
				$retvals[$ctrl]['explanation'] = \Arr::get($ctrl::$locomo, 'explanation', '') ;
				$main_action = \Arr::get($ctrl::$locomo, 'main_action', '');
				$retvals[$ctrl]['main_action'] = $main_action ? $ctrl.DS.$main_action : '' ;
				$retvals[$ctrl]['show_at_menu'] = \Arr::get($ctrl::$locomo, 'show_at_menu', true) ;
				$retvals[$ctrl]['is_for_admin'] = \Arr::get($ctrl::$locomo, 'is_for_admin', false) ;
				$retvals[$ctrl]['no_acl'] = \Arr::get($ctrl::$locomo, 'no_acl', false) ;
				$retvals[$ctrl]['widgets'] = \Arr::get($ctrl::$locomo, 'widgets') ;
				$retvals[$ctrl]['order'] = \Arr::get($ctrl::$locomo, 'order', 100) ;
			}

			// order
			$retvals = \Arr::multisort($retvals, array('order' => SORT_ASC,));

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
		$files = \File::read_dir($dir, 1);
		sort($files);
		foreach ($files as $k => $file)
		{
			if ( ! is_numeric($file[0])) unset($files[$k]);
		}
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
	public static function get_fiscal($date = null)
	{
		if (preg_match('/^([0-9]{4})$/', $date)) $date = date('Y-n-d', strtotime($date . '-04-01'));
		//var_dump(date('Y-n', strtotime($date))); die();
		!$date and $date = date('Y-m-d');

		if (date('n', strtotime($date)) < 4)
		{
			$year = date('Y', strtotime($date)) - 1;
		}
		else
		{
			$year = date('Y', strtotime($date));
		}

		return \DB::expr('"' . $year . '-04-01 00:00:00"' . ' and ' . '"' . ($year+1) . '-03-31 23:59:59"');
	}

	/*
	 * convert_jp_era()
	 * 西暦と和暦の変換
	 * @return string
	 */
	public static function convert_jp_era($str, $ad2jp = TRUE)
	{
		$str = mb_convert_kana($str, "a", "UTF-8");

		//4文字だけだったら年のみと見なして1月1日で換算する
		$is_short_way = FALSE;
		if (strlen($str))
		{
			$str.= '-01-01';
			$is_short_way = TRUE;
		}

		//vals
		$timestr = strtotime($str);
		$date = date('Ymd', $timestr);
		$y = date('Y', $timestr);
		$m = date('m', $timestr);
		$d = date('d', $timestr);
		$date = intval($date) ;
		$y = intval($y) ;
		$retvals = array() ;

		//西暦 -> 和暦
		if ($ad2jp)
		{
			if ($date <= '19120729')
			{
				$jpk = '明治';
				$jp = 'M';
				$yy = $y - 1867;
			}
			elseif ($date >= '19120730' && $date <= '19261224')
			{
				$jpk = '大正';
				$jp = 'T';
				$yy = $y - 1911;
			}
			elseif ($date >= '19261225' && $date <= '19890107')
			{
				$jpk = '昭和';
				$jp = 'S';
				$yy = $y - 1925;
			}
			elseif ($date >= '19890108')
			{
				$jpk = '平成';
				$jp = 'H';
				$yy = $y - 1988;
			}

			//平成元年を特別扱い
			$jpk2 = '' ;
			$jp2 = '' ;
			$yy2 = '' ;
			if ($y == 1989)
			{
				$jpk2 = ' - 平成';
				$jp2 = ' - H';
				$yy2 = $y - 1988;
			}
		}
		//和暦 -> 西暦
		else
		{
			//準備中
		}

		//retval
		$retvals['jp']['full']    = $jpk.$yy.'年'.$m.'月'.$d.'日' ;
		$retvals['jp']['full2']   = $jp.$yy.'年'.$m.'月'.$d.'日' ;
		$retvals['jp']['y']       = $jpk.$yy.'年' ;
		$retvals['jp']['y2']      = $jp.$yy.'年' ;
		$retvals['jp']['y3']      = $jp.$yy ;
		$retvals['jp']['y_long']  = $jpk2 ? $jpk.$yy.'年'.$jpk2.$yy2.'年' : $jpk.$yy.'年' ;
		$retvals['jp']['y2_long'] = $jpk2 ? $jp.$yy.'年'.$jp2.$yy2.'年' : $jp.$yy.'年' ;
		$retvals['jp']['y3_long'] = $jpk2 ? $jp.$yy.$jp2.$yy2 : $jp.$yy ;

		return $retvals;
	}

	/**
	 * get_file_list
	 */
	public static function get_file_list($dir, $type = 'all')
	{
		$iterator = new \RecursiveDirectoryIterator($dir);
		$iterator = new \RecursiveIteratorIterator($iterator);
		$siterator = new \Locomo\SortedIterator($iterator);

		$list = array();
		// $fileinfo is SplFiIeInfo Object
		foreach ($siterator as $fileinfo)
		{
//			if ($type == 'all'  && ! $fileinfo->isFile() && ! $fileinfo->isDir()) continue;
			if ($type == 'file' && ! $fileinfo->isFile()) continue;
			if ($type == 'dir'  && ! $fileinfo->isDir()) continue;

			$path = $fileinfo->getPathname();
			$basename = basename($path);
			if (substr($basename, 0, 2) == '..') continue;
			$path = \Str::ends_with($basename, '.') ? substr($path, 0, -1) : $path; // current dir
			if (! \Str::ends_with($basename, '.') && substr($basename, 0, 1) == '.') continue; // invisible file
			$list[] = $path;
		}

		return $list;
	}

	/**
	 * get_locomo
	 * コントローラ（ない場合は親クラスの）の$locomoの任意の値を取得
	 * fetch $locomo value
	 * @return Mix
	 */
	public static function get_locomo($controller, $property = null, $default = false)
	{
		if ( ! class_exists($controller)) return $default;
		if ( ! property_exists($controller, 'locomo')) return $default;

		// locomos
		$locomos = $controller::$locomo;
		if (is_null($property)) return $locomos;

		// try to search parent
		$parent = get_parent_class($controller);
		if (is_null(\Arr::get($locomos, $property)) && class_exists($parent))
		{
			return static::get_locomo($parent, $property, $default);
		}
		return \Arr::get($locomos, $property, $default);
	}

	/**
	 * uniform_locomopath
	 * [\Namespace]\Controller_Name/actionの形式を整える。Windows環境では、DSが、バックスラッシュになっているため、actionsetのdependenciesをDSを使って定義していると、locomoパスの一意性が失われてしまう。これを防止するため、locomoパスのアクション名の前をスラッシュに整える。
	 * at Windows environment, DS means backslash. this cause break uniqueness of locomo-path. so unity style of locomo-path.
	 * @return string
	 */
	public static function uniform_locomopath($str = '')
	{
		$last_backslash_pos = strrpos($str, '\\');

		// Locomoパスは、必ずスラッシュの後にアクションを伴うので、最後に出現するバックスラッシュが先頭の場合は、正しいLocomoパスか、たいへん不正な値かのどちらかなので、とりあえずそのまま返す。
		if ($last_backslash_pos === 0 || $last_backslash_pos == false) return $str;

		$slash_pos = strpos($str, '/');
		// スラッシュが存在する場合は、問題がないので、そのまま返す。
		if ($slash_pos !== false) return $str;

		// バックスラッシュが先頭以外に存在し、かつスラッシュがない場合、最後のバックスラッシュはスラッシュに変換する
		$str[$last_backslash_pos] = '/';
		return $str;
	}

	/**
	 * method_exists
	 * コントローラのメソッドの存在確認
	 * @return Mix
	 */
	public static function method_exists($locomo_path)
	{
		// check class_exists
		$module = \Inflector::get_modulename($locomo_path);
		$module && \Module::loaded($module) == false and \Module::load($module);

		// controller and action
		$locomo_path = \Inflector::add_head_backslash($locomo_path);
		list($controller, $action) = explode('/', $locomo_path);

		if (class_exists($controller))
		{
			if (
				! method_exists($controller, 'action_'.$action) &&
				! method_exists($controller, 'get_'.$action) &&
				! method_exists($controller, 'post_'.$action)
			)
			{
				return false;
			}
		} else {
			return false;
		}
		return true;
	}

	/**
	 * parse_args
	 * $defaults の中にある key のもののみ
	 * かつ、値は $args のほうを使用した配列を返す
	 * 戻り値をextract で使うとき等に使用
	 */
	public static function parse_args($args, $defaults = array())
	{
		/*
		 * object が来るかも?
		if ( is_object( $args ) )
			$r = get_object_vars( $args );
		elseif ( is_array( $args ) )
			$r =& $args;
		 */

		$args = array_merge($defaults, $args);
		$ret = array_intersect_key($args, $defaults);

		return $ret;
	}

	/*
	 * parse_email()
	 */
	public static function parse_email ($str = '')
	{
		$str = str_replace("\r", "", $str);
		$headers_raw = trim(substr($str, 0, strpos($str, "\n\n")));
		$body = trim(substr($str, strpos($str, "\n\n")));

		$headers = array();
		foreach (explode("\n", $headers_raw) as $header)
		{
			list($k, $v) = explode(':', $header);
			if (strpos($v, '<') !== false)
			{
				$vv = substr($v, 0, strpos($v, '<'));
				$key = trim($k).'_str';
				$headers[$key] = trim($vv);
				$vv = substr($v, strpos($v, '<'), strpos($v, '>'));
				$key = trim($k).'_email';
				$headers[$key] = trim(trim($vv, ">"), "<");
			}
			else
			{
				$headers[trim($k)] = trim($v);
			}
		}
		return array('headers' => $headers, 'body' => $body);
	}
}
