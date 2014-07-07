<?php
namespace Kontiki;
abstract class Controller extends \Fuel\Core\Controller_Rest
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

		//actionset
		$this->set_actionset();

		//model_name
		$this->model_name  = '\\'.ucfirst($this->request->module).'\\Model_'.ucfirst($this->request->module);

		//nicename
		$controller = '\\'. (string) \Request::main()->controller;
		self::$nicename  = $controller::$nicename;

		//acl まずユーザ／ユーザグループ単位を確認する。
		if( ! $this->acl(\User\Controller_User::$userinfo)):
			//ユーザ／ユーザグループで失敗したら、オーナ権限を確認する
			if( ! $this->owner_acl(\User\Controller_User::$userinfo)):
				//双方駄目ならエラー
				\Session::set_flash('error', $this->messages['auth_error']);
				\Response::redirect(\Uri::base(false));
			endif;
		endif;
	}

	/**
	* after()
	*/
	public function after($response)
	{
		return $response;
	}

	/**
	* acl()
	*/
	public function acl($userinfo)
	{
		return \Acl\Controller_Acl::auth($this->request->module, $this->request->action, $userinfo);
	}

	/**
	* owner_acl()
	*/
	public function owner_acl($userinfo)
	{
		//adminたち（ユーザグループ-1や-2）は常にtrue
		if(in_array(-2, $userinfo['usergroup_ids']) || in_array(-1, $userinfo['usergroup_ids'])) return true;

		//オーナ権限を判定するコントローラには、creator_idフィールドが必須とする（creator_idを使わないとしても）
		$model = $this->model_name;
		if( ! \DBUtil::field_exists($model::get_table_name(), array('creator_id'))) return false;

		//オーナ権限なのでゲストは常にfalse
		if( ! \User\Controller_User::$is_user_logged_in) return false;

		//オーナ権限に関係あるアクションセットを取得
		$actionset4owner = array();
		foreach(self::$actionset_owner as $actionset_name => $action):
			$actionset4owner = array_merge($actionset4owner, $action['dependencies']);
		endforeach;

		//オーナ権限の関係ないアクションであれば常にfalse
		if( ! in_array($this->request->action, $actionset4owner)) return false;

		//オーナ権限のあるアクションは原理的に個票なので第一引数はid
		$id = is_numeric(\URI::segment(3)) ? \URI::segment(3) : \URI::segment(4);
		if( ! $id) return false;

		//オーバヘッドだが権限確認用に取得
		$model = $this->model_name ;
		$item = $model::find_item_anyway($id);
		if( ! $item) return false;

		//adminでないのに、user_idがなかったり、コンテンツのcreator_idがなければfalse
		if( ! $userinfo['user_id'] || ! $item->creator_id) return false;

		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));
		return $this->check_owner_acl($controller, $this->request->action, $userinfo, $item);
	}

	/**
	 * check_owner_acl()
	 * オーナ権限は原則creator_idを確認するが、他を確認することがあるときはこのメソッドをオーバライドすること
	*/
	public function check_owner_acl($controller = null, $action = null, $userinfo = null, $item = null)
	{
		if($userinfo == null || $item == null || $controller == null || $action == null) return false;

		//aclテーブルを確認して、アクションがなければ、false
		if( ! \Acl\Controller_Acl::owner_auth($controller, $action, $userinfo, $item)) return false;

		//アクションがあったらcreator_idとログイン中のuser_idを比較
		return ($userinfo['user_id'] === $item->creator_id);
	}

	/**
	 * set_actionset()
	 */
	public function set_actionset()
	{
		self::$actionset = \Kontiki\Actionset::actionItems($this->request->module);
		self::$actionset_owner = \Kontiki\Actionset_Owner::actionItems($this->request->module);
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
				switch($v):
					case 'text':
						$val = $this->request->module.'-'.$k.'-'.md5(microtime()) ;
						break;
					case 'email':
						$val = $this->request->module.'-'.$k.'-'.md5(microtime()).'@example.com' ;
						break;
					case 'int':
						$val = 1 ;
						break;
					case 'date':
						$val = date('Y-m-d') ;
						break;
					case 'datetime':
						$val = date('Y-m-d H:i:s') ;
						break;
					case 'geometry':
						$val = "GeomFromText('POINT(138.72777769999993 35.3605555)')" ;//Mt. fuji
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
