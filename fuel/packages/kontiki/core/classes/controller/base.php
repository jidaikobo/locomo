<?php
namespace Kontiki_Core;
// extends を　Controller_Hybrid に
class Controller_Base extends \Fuel\Core\Controller_Rest
{
	/**
	* @var string name for human
	*/
	public static $nicename = '';

	/**
	 * @var string model name
	 */
	protected $model_name = '';

	/**
	* @var string current_action
	*/
	protected $current_action = '';

	/**
	* @var string current_id
	*/
	public static $current_id = '';

	/**
	 * @var array set by self::set_actionset()
	 */
	public static $actionset = array();
	public static $actionset_owner = array();

	/**
	* @var test datas
	* array fieldname => 'type(text|email|int|date|datetime|geometry)'
	*/
	protected $test_datas = array();

	/**
	* before()
	*/
	public function before()
	{
		//parent
		parent::before();

		//テンプレートの検索パスを追加
		$request = \Request::active();
		$request->add_path(PKGPROJPATH.'views'.DS.$this->request->module.DS,true);

		//ユーザ情報のセット
		\User\Controller_User::set_userinfo();

		//current_actionのセット
		//HMVCの場合は、呼ばれたモジュールに応じたものにかわる
		$this->current_action = $this->request->module.DS.$this->request->action ;

		//model_name
		$controller = ucfirst($this->request->module);
		$this->model_name = '\\'.$controller.'\\Model_'.$controller;

		//nicename 人間向けのモジュール名
		$controllers_from_config = \Config::load($controller);
		self::$nicename = $controllers_from_config['nicename'];
	}

	/**
	 * router()
	*/
	public function router($method, $params)
	{
		$userinfo = \User\Controller_User::$userinfo;

		//ユーザ／ユーザグループ単位のACLを確認する。
		$is_allow = \Acl\Controller_Acl::auth($this->current_action, $userinfo);

		//権限がなくても、オーナACLがある行為だったらいったん留保
		//modelのauthorized_option()に判断をゆだねる
		if( ! $is_allow):
			$is_allow = \Acl\Controller_Acl::is_exists_owner_auth($this->request->module, $method);
		endif;

		if( ! $is_allow):
			$page = \Request::forge('content/403')->execute();
			return new \Response($page, 403);
		endif;

		//ログイン画面をトップページにする処理
		$use_login_as_top = \Config::get('use_login_as_top');
		if(
			$use_login_as_top && //configで設定
			$userinfo['user_id'] == 0 && //ログイン画面はゲスト用
			$this->request->module.DS.$method == 'content/home' //トップページを求められているとき
		):
			return \Response::redirect(\Uri::create('user/login'));
		endif;

		//通常の処理に渡す
		return parent::router($method, $params);
	}

	/**
	 * set_actionset()
	 */
	public static function set_actionset($obj = null)
	{
		$controller = \Request::main()->module;

		//アクションセットの設定
		$actionset = \Util::get_valid_actionset_name($controller);
		$actionset_owner = \Util::get_valid_actionset_name($controller, $is_owner = true);

		if(class_exists($actionset))
			self::$actionset = $actionset::actionItems($obj);

		if(class_exists($actionset_owner))
			self::$actionset_owner = $actionset_owner::actionItems($obj);
	}

	/**
	 * get_actionset()
	 * メニュー生成のため、viewmodelから呼ばれます
	 */
	public static function get_actionset($obj = null)
	{
		if(static::$actionset) return static::$actionset;
		static::set_actionset($obj);
		return static::$actionset;
	}

	/**
	 * action_add_testdata()
	 */
	public function action_add_testdata($num = 10)
	{
		//only at development
		if(\Fuel::$env != 'development') die();

		//$test_datas
		if(empty($this->test_datas)):
			\Session::set_flash('error', 'need to prepare test_data proparty.');
			\Response::redirect($this->request->module);
		endif;

		$model = $this->model_name ;

		for($n = 1; $n <= $num; $n++):
			foreach($this->test_datas as $k => $v):
				$type = $v;
				$default = null;
				//test_datasにコロンがあったらデフォルト文字列と見なす
				if(strpos($v,':')):
					list($type, $default) = explode(':', $v);
				endif;

				switch($type):
					case 'text':
						$val = $default ? $default : $this->request->module.'-'.$k.'-'.md5(microtime()) ;
						break;
					case 'email':
						$val = $default ? $default : $this->request->module.'-'.$k.'-'.md5(microtime()).'@example.com' ;
						break;
					case 'int':
						$val = $default ? $default : 1 ;
						break;
					case 'date':
						$val = $default ? $default : date('Y-m-d') ;
						break;
					case 'datetime':
						$val = $default ? $default : date('Y-m-d H:i:s') ;
						break;
					case 'geometry':
						$val = $default ? $default : "GeomFromText('POINT(138.72777769999993 35.3605555)')" ;//Mt. fuji
						break;
				endswitch;
				$args[$k] = $val;
			endforeach;
			$obj = $model::forge($args);
			$obj->save();
		endfor;
		\Session::set_flash('success', 'added '.$num.' datas.');
		\Response::redirect($this->request->module);
	}
}
