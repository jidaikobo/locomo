<?php
namespace Locomo;
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

		//profile表示はrootだけ（当然ながらConfigでtrueだったら計測はされる）
		if(\User\Controller_User::$userinfo['user_id'] >= -1)
			\Fuel::$profiling = false;

		//current_actionのセット
		//HMVCの場合は、呼ばれたモジュールに応じたものにかわる
		$this->current_action = $this->request->module.DS.$this->request->action ;

		//model_name
		$controller = ucfirst($this->request->module);
		$this->model_name = '\\'.$controller.'\\Model_'.$controller;

		//nicename 人間向けのモジュール名
		$controllers_from_config = \Config::load($controller);
		self::$nicename = $controllers_from_config['nicename'];

		//actionset
		\Actionset::forge($this->request->module);
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
			$userinfo['user_id'] == 0 && //ログイン画面はゲスト用
			$this->request->module.DS.$method == 'content/home' //トップページを求められているとき
		):
			return \Response::redirect(\Uri::create('user/login'));
		endif;

		//通常の処理に渡す
		return parent::router($method, $params);
	}

	/**
	 * action_add_testdata()
	 */
	public function action_add_testdata($num = 10)
	{
		//only at development
		if(\Fuel::$env != 'development') die();
		if(\User\Controller_User::$userinfo['user_id'] !== -2) die('forbidden');

		//$test_datas
		$model = $this->model_name;
		$form = $model::form_definition('add_testdata');
		if(!$form):
			\Session::set_flash('error', 'form_definition failed.');
			\Response::redirect($this->request->module);
		endif;

		//save
		$args = array();
		for($n = 1; $n <= $num; $n++):
			foreach($form->field() as $property => $v):
				if(
					\Arr::search($v->rules, 'required', null, true) != true &&
					\Arr::search($v->rules, 'require_once', null, true) != true //original
				){
					continue;
				}

				$str = md5(microtime());
				/*
				//do nothing
				match_value
				match_pattern
				match_field
				valid_string
				min_length
				*/
				$rules = \Arr::assoc_to_keyval($v->rules, 0, 1);
	
				//exact_length
				if($each_rule = \Util::get(@$rules['exact_length'])){
					$str = substr($str, 0, intval($exact_length[0]));
				}
	
				//max_length
				if($each_rule = \Util::get(@$rules['max_length'])){
					$str = substr($str, 0, intval($each_rule[0]));
				}
	
				//valid_email
				$each_rule = \Util::get(@$rules['valid_email']);
				$str.= $each_rule !== false ? '@example.com' : '' ;
	
				//valid_emails
				$each_rule = \Util::get(@$rules['valid_emails']);
				$str.= $each_rule !== false ? '@example.com' : '' ;
	
				//valid_date
				$each_rule = \Util::get(@$rules['valid_date']);
				$str = $each_rule !== false ? date('Y-m-d H:i:s') : $str ;
	
				//valid_url
				$each_rule = \Util::get(@$rules['valid_url']);
				$str = $each_rule !== false ? 'http://example.com' : $str ;
	
				//valid_ip
				$each_rule = \Util::get(@$rules['valid_ip']);
				$str = $each_rule !== false ? '1.1.1.1' : $str ;
	
				//numeric_min
				$each_rule = \Util::get(@$rules['numeric_min']);
				$str = $each_rule !== false ? intval($each_rule[0]) : $str ;
	
				//numeric_max
				$each_rule = \Util::get(@$rules['numeric_max']);
				$str = $each_rule !== false ? intval($each_rule[0]) : $str ;
	
				//numeric_between
				$each_rule = \Util::get(@$rules['numeric_between']);
				$str = $each_rule !== false ? intval($each_rule[0]) : $str ;
	
				$args[$property] = $str;
	
			endforeach;
			if(! $args) continue;
			$obj = $model::forge($args);
			$obj->save();
		endfor;

		if(! $args):
			\Session::set_flash('error', 'couldn not add datas');
		else:
			\Session::set_flash('success', 'added '.$num.' datas.');
		endif;
		return \Response::redirect($this->request->module.DS.'index_admin');
	}
}
