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

		//アクションセットのセット
		$this->set_actionset($this->request->module);
	}

	/**
	 * router()
	*/
	public function router($method, $params)
	{
		//ユーザ／ユーザグループ単位のACLを確認する。
		if( ! $this->acl(\User\Controller_User::$userinfo)):
			$page = \Request::forge('content/403')->execute();
			return new \Response($page, 403);
		endif;

		//ログイン画面をトップページにする処理
		$use_login_as_top = \Config::get('use_login_as_top');
		if(
			$use_login_as_top && //configで設定
			\User\Controller_User::$userinfo['user_id'] == 0 && //ログイン画面はゲスト用
			$this->request->module.DS.$method == 'content/home' //トップページを求められているとき
		):
			return \Response::redirect(\Uri::create('user/login'));
		endif;

		//アクションセットで定義されていないアクションへのアクセスの拒否
		//$this->current_actionにはいくつかの可能性があるので検査用に配列を準備
		$current_actions = array();
		foreach(\Uri::segments() as $param):
			$uris[] = $param;
			$current_actions[] = join('/',$uris);
		endforeach;

		//current_actionsが存在しないときにはhome_urlを判定対象とする
		$current_actions = $current_actions ? $current_actions : array(\Config::get('home_url')) ;

		//コントローラに存在するアクションセットを確認
		//アクションセットの配列から「モジュール名/アクション」の文字列を取得
		//それにalways_allowedを足す
		$actionsets = array();
		$func = function($v) { return $this->request->module.'/'.$v; };
		foreach(self::$actionset as $actionset):
			$temp = array_map($func, $actionset['dependencies']);
			$actionsets = array_merge($actionsets, $temp);
		endforeach;
		$actionsets = array_merge($actionsets, \Config::get('always_allowed'));

		//アクションセットを走査し、存在しないアクションセットへのアクセスはbanする
		$is_allow = false;
		foreach($current_actions as $each_current_action):
			if(in_array($each_current_action, $actionsets) ):
				$is_allow = true;
				break;
			endif;
		endforeach;
		//なければ404
		if( ! $is_allow ){
			$page = \Request::forge('content/404')->execute();
			return new \Response($page, 404);
		}

		//通常の処理に渡す
		return parent::router($method, $params);
	}

	/**
	* acl()
	*/
	public function acl($userinfo)
	{
		//adminたち（ユーザグループ-1や-2）は常にtrue
		if(
			in_array(-2, $userinfo['usergroup_ids']) ||
			in_array(-1, $userinfo['usergroup_ids'])
		)
		return true;

		//auth
		return \Acl\Controller_Acl::auth($this->current_action, $userinfo);
	}

	/**
	* owner_acl()
	* オーバライドの例はuserモジュール参照。
	*/
	public function owner_acl($userinfo = null, $current_action = null, $item = null)
	{
		if($userinfo == null || $current_action == null || $item == null) return false;

		//adminたち（ユーザグループ-1や-2）は常にtrue
		if(in_array(-2, $userinfo['usergroup_ids']) || in_array(-1, $userinfo['usergroup_ids'])) return true;

		//オーナ権限を判定するコントローラには、creator_idフィールドが必須とする（creator_idを使わないとしても）
		$model = $this->model_name;
		if( ! $model):
			$current_actions = explode('/',$current_action);//アクションが三つ目の引数にくる場合もあるのでlist()不可。
			$model = '\\'.ucfirst($current_actions[0]).'\\Model_'.ucfirst($current_actions[0]);
		endif;
		if( ! \DBUtil::field_exists($model::get_table_name(), array('creator_id'))) return false;

		//オーナ権限なのでゲストは常にfalse
		if( ! \User\Controller_User::$is_user_logged_in) return false;

		//acls_owerがなければ、false
		$current_action = $current_action ? $current_action : $this->request->module.'/'.$this->request->action ;
		if( ! \Acl\Controller_Acl::owner_auth($current_action, $userinfo)) return false;

		//user_idがなかったり、コンテンツのcreator_idがなければfalse
		if( ! $userinfo['user_id'] || ! $item->creator_id) return false;

		//creator_idとログイン中のuser_idを比較してreturn
		return ($userinfo['user_id'] === $item->creator_id);
	}

	/**
	 * set_actionset()
	 */
	public function set_actionset($controller = null, $id = null)
	{
		is_null($controller) and die('set_actionset() needs controller');

		//アクションセット用のファイルを取得
		$default_path  = PKGCOREPATH."modules/{$controller}/classes/actionset/actionset.php";
		$override_path = PKGPROJPATH ."modules/{$controller}/classes/actionset/actionset.php";

		if(file_exists($override_path)):
			require_once($override_path);
			require_once(PKGPROJPATH."modules/{$controller}/classes/actionset/actionset_owner.php");
		else:
			require_once($default_path);
			require_once(PKGCOREPATH."modules/{$controller}/classes/actionset/actionset_owner.php");
		endif;

		//アクションセットの設定
		$actionset_class = \Kontiki\Util::get_valid_actionset_name($controller);
		$actionset_owner_class = \Kontiki\Util::get_valid_actionset_name($controller, $is_owner = true);
		self::$actionset = $actionset_class::actionItems($controller, $id);
		self::$actionset_owner = $actionset_owner_class::actionItems($controller, $id);
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
