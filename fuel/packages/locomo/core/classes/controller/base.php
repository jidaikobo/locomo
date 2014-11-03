<?php
namespace Locomo;
class Controller_Base extends \Fuel\Core\Controller_Hybrid
{
	/**
	* @var string name for human
	*/
	public static $nicename = '';

	/**
	* @var string template
	*/
	public $template = 'default';

	/**
	 * @var string model name
	 */
	protected $model_name = '';

	/**
	* @var string current_action
	*/
	protected $current_action = '';

	/**
	 * @var config
	 */
	protected $config = array();

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

	protected static $views_path_module = '';

	/**
	* before()
	*/
	public function before()
	{
		//parent
		parent::before();

		//テンプレートの検索パスを追加
		$request = \Request::active();

		$views_path_module = static::$views_path_module ?: $this->request->module;

		$request->add_path(PKGPROJPATH.'views'.DS.$views_path_module.DS,true);
		$request->add_path(PKGPROJPATH.'modules'.DS.$views_path_module.DS,true);

		//ユーザ情報のセット
		\Auth::set_userinfo();

		//profile表示はrootだけ（当然ながらConfigでtrueだったら計測はされる）
		if(\Auth::get_user_id() >= -1)
			\Fuel::$profiling = false;

		//current_actionのセット
		//HMVCの場合は、呼ばれたモジュールに応じたものにかわる
		$this->current_action = $this->request->module.DS.$this->request->action ;

		//model_name
		$controller = ucfirst($this->request->module);
		$this->model_name = '\\'.$controller.'\\Model_'.$controller;

		//nicename 人間向けのモジュール名
		$this->config = \Config::load($controller);
		self::$nicename = $this->config['nicename'];

		//actionset
		\Actionset::forge($this->request->module);
	}

	/**
	 * router()
	*/
	public function router($method, $params)
	{
		$userinfo = \Auth::get_userinfo();

		//ユーザ／ユーザグループ単位のACLを確認する。
		$is_allow = \Auth::auth($this->current_action, $userinfo);

		//権限がなくても、オーナACLがある行為だったらいったん留保
		//modelのauthorized_option()に判断をゆだねる
		if( ! $is_allow):
			$is_allow = \Auth::is_exists_owner_auth($this->request->module, $method);
		endif;

		//存在しないアクション
		if(
			! method_exists(get_called_class(), 'action_'.$method) &&
			! method_exists(get_called_class(), 'get_'.$method)
		){
			$is_allow = false;
			\Session::set_flash('error','"'.htmlspecialchars($method, ENT_QUOTES).'" is not exist.');
		}

		if( ! $is_allow):
			$page = \Request::forge('content/403')->execute();
			return new \Response($page, 403);
		endif;

		//ログイン画面をトップページにする処理
		$use_login_as_top = \Config::get('use_login_as_top');
		if(
			$use_login_as_top && //configで設定
			\Auth::get_user_id() == 0 && //ログイン画面はゲスト用
			$this->request->module.DS.$method == 'content/home' //トップページを求められているとき
		):
			return \Response::redirect(\Uri::create('user/login'));
		endif;

		//通常の処理に渡す
		return parent::router($method, $params);
	}
}

