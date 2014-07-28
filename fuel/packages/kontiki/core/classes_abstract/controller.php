<?php
namespace Kontiki;
abstract class Controller_Abstract extends \Fuel\Core\Controller_Rest
{
	/**
	* @var string name for human
	*/
	public static $nicename = '';

	/**
	 * @var string model name
	 */
	protected $model_name  = '';

	/**
	* @var string current_id
	*/
	public static $current_id = '';

	/**
	 * @var array set by self::set_actionset()
	 */
	public static $actionset  = array();
	public static $actionset_owner  = array();

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

		//set and get userinfo
		\User\Controller_User::set_userinfo();
		$userinfo = \User\Controller_User::$userinfo;

		//model_name
		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));
		$this->model_name  = '\\'.ucfirst($controller).'\\Model_'.ucfirst($controller);

		//actionset
		$this->set_actionset();

		//set_current_id
		$this->set_current_id();

		//可能であれば、とりあえず取得してみる
		$item = false;
		if(self::$current_id):
			$model = $this->model_name;
			$item = $model::find_item_anyway(self::$current_id);
		endif;

		//nicename

		$controllers_from_config = \Config::load($controller);
		self::$nicename = $controllers_from_config['nicename'];

		$is_allowed = false;
		//acl まずユーザ／ユーザグループ単位を確認する。
		$is_allowed = $this->acl($userinfo) ? true : $is_allowed ;

		//ユーザ／ユーザグループで失敗したら、オーナ権限を確認する
		if( ! $is_allowed && $item):
			$current_action = $this->request->module.'/'.$this->request->action ;
			$is_allowed = $this->owner_acl($userinfo, $current_action, $item) ? true : $is_allowed ;
		endif;

		//双方駄目ならエラー
		if( ! $is_allowed):
			\Session::set_flash('error', $this->messages['auth_error']);
			\Response::redirect(\Uri::base(false));
		endif;
	}

	/**
	 * set_current_id()
	*/
	public function set_current_id()
	{
		$id_segment = @self::$actionset->{$this->request->action}['id_segment'];
		if( ! $id_segment) return null;
		self::$current_id = \URI::segment($id_segment);
	}

	/**
	* after()
	*/
	public function after($response)
	{
		return $response;
	}

	/**
	 * router()
	 * コントローラを短く書くための独自ルーティング
	 * modules/MODNAME/classes/controller/MODNAME_ACTNAME.phpで、個別のアクションを書けるようにする
	 * アクションセットで定義されていないアクションへのアクセスの拒否
	*/
	public function router($method, $params)
	{
		//アクションセットで定義されていないアクションへのアクセスの拒否
//self::$actionset


		//アクションが普通に存在していれば、そのまま実行
		$class = "{$this->request->module}_{$method}";
		$file = PKGPATH."kontiki/modules/{$this->request->module}/classes/controller/{$class}.php";
		if(method_exists($this, 'action_'.$method)):
			return parent::router($method, $params);
		//個別アクションファイルがあったらそれを実行
		elseif(file_exists($file)):
			require($file);
			$request = \Request::forge();
			$class = "\\".ucfirst($this->request->module)."\\Controller_".\Inflector::words_to_upper($class);
			$action = "action_".$method;
			if( ! class_exists($class) || ! method_exists($class, $action))
				\Response::redirect(\Uri::base());
			$controller_obj = new $class($request);
			return $controller_obj->$action($params);
		endif;
	}

	/**
	* acl()
	*/
	public function acl($userinfo)
	{
		//adminたち（ユーザグループ-1や-2）は常にtrue
		if(in_array(-2, $userinfo['usergroup_ids']) || in_array(-1, $userinfo['usergroup_ids'])) return true;

		//auth
		$current_action = $this->request->module.'/'.$this->request->action;
		return \Acl\Controller_Acl::auth($current_action, $userinfo);
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
		self::$actionset = \Kontiki\Actionset::actionItems($controller, $id);
		self::$actionset_owner = \Kontiki\Actionset_Owner::actionItems($controller, $id);
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
