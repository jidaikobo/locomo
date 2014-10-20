<?php
namespace Kontiki_Core_Module\User;
class Controller_User extends \Kontiki\Controller_Crud
{
	//trait
	use \Option\Controller_Option;

	/**
	* @var user information
	*/
	public static $userinfo = array();

	/**
	* @var bool is_user_logged_in
	*/
	public static $is_user_logged_in = false;

	/**
	 * test datas
	 */
	protected $test_datas = array(
		'user_name'   => 'text',
		'password'    => 'text:test',
		'email'       => 'email',
		'status'      => 'text:public',
		'creator_id'  => 'int',
		'modifier_id' => 'int',
	);

	/**
	 * set_actionset()
	 */
	public static function set_actionset($obj = null)
	{
		parent::set_actionset($obj);
		unset( self::$actionset->workflow_actions );
	}

	/**
	 * revision_modify_data()
	 */
	public function revision_modify_data($obj, $mode = null)
	{
		if($mode == 'insert_revision'):
			$usergroups = is_array(\Input::post('usergroups')) ? \Input::post('usergroups') : array();
			$obj->usergroups = $usergroups;
		endif;
		return $obj;
	}

	/**
	 * post_save_hook()
	 */
	public function post_save_hook($obj = NULL, $mode = 'edit')
	{
		$obj = parent::post_save_hook($obj, $mode);

		//usergroups
		$model = $this->model_name;
		$model::update_options_relations('usergroups', $obj->id);

		return $obj;
	}

	/**
	 * set_userinfo()
	 * ログイン中のユーザ情報のセット。
	 * \Kontiki\Controller_Base::before()から呼ばれる。
	 */
	public static function set_userinfo()
	{
		//set userinfo
		$session = \Session::instance();
		self::$userinfo = $session->get('user');

		//is_user_logged_in
		self::$is_user_logged_in = (self::$userinfo) ? true : false;

		//guest
		if( ! self::$userinfo['user_id']):
			self::$userinfo['user_id'] = 0;
		endif;
		self::$userinfo['usergroup_ids'][] = 0;

		//acl
		$acls = array(\Config::get('home_url'));

		$acl_tmp = \Acl\Model_Acl::find('all',
			array(
				'where' => array(array('usergroup_id', 'IN' , self::$userinfo['usergroup_ids']))
			)
		);

		foreach($acl_tmp as $v):
			$acls[] = $v->controller .'/'.$v->action;
		endforeach;

		self::$userinfo['acls'] = array_unique($acls);
	}

	/**
	 * action_login()
	 */
	public function action_login($redirect = NULL)
	{
		//redirect
		$redirect_decode = $redirect ? base64_decode($redirect) : \URI::base() ;

		//ログイン済みのユーザだったらログイン画面を表示しない
		if(self::$is_user_logged_in):
			\Session::set_flash( 'error', 'あなたは既にログインしています');
			\Response::redirect($redirect_decode);
		endif;

		//ログイン処理
		if(\Input::method() == 'POST'):
			//Banされたユーザだったら追い返す
			$user_model = \User\Model_User::forge();
			if( ! $user_model::check_deny(\Input::post("account"))):
				$user_ban_setting = \Config::get('user_ban_setting');
				$limit_count = $user_ban_setting ? $user_ban_setting['limit_count'] : 3 ;
				$limit_time  = $user_ban_setting ? $user_ban_setting['limit_time'] : 300 ;
				\Session::set_flash( 'error', "{$limit_count}回のログインに失敗したので、{$limit_time}秒間ログインはできません。");
				\Response::redirect('/');
			endif;

			$account = \Input::post('account');
			$password = \Input::post('password');
			if($account == null || $password == null):
				\Session::set_flash( 'error', 'ユーザ名とパスワードの両方を入力してください');
				\Response::redirect('user/login/');
			endif;
			//flag and value
			$user = array();

			//rootユーザ
			if($account == ROOT_USER_NAME && $password == ROOT_USER_PASS):
				$user['user_id']       = -2;
				$user['user_name']     = 'root';
				$user['display_name']  = 'root権限管理者';
				$user['usergroup_ids'] = array(-2);
			endif;

			//adminユーザ
			if($account == ADMN_USER_NAME && $password == ADMN_USER_PASS):
				$user['user_id']       = -1;
				$user['user_name']     = 'admin';
				$user['display_name']  = '管理者';
				$user['usergroup_ids'] = array(-1);
			endif;

			//データベースで確認
			if( ! $user || ! @is_numeric($user['user_id'])):
				$user_obj = Model_User::find('first', array(
					'where' => array(
						array('password', '=', Model_User::hash($password)),
						array('created_at', '<=', date('Y-m-d H:i:s')),
						array('expired_at', '>=', date('Y-m-d H:i:s')),
						array(
							array('user_name', '=', $account),
							'or' => array('email', '=', $account),
						)),
					)
				);
			if ($user_obj) {
				$user['user_id']       = $user_obj->id;
				$user['user_name']     = $user_obj->user_name;
				$user['display_name']  = $user_obj->display_name;
				$user['usergroup_ids'] = array_keys($user_obj->usergroup);
			}
			endif;

			//ログイン成功
			if($user):
				//session
				$session = \Session::instance();
				$session->set('user', $user);
				$user_model::add_user_log($account, $password, true);

				//redirect
				\Session::set_flash( 'success', 'ログインしました。');
				\Response::redirect($redirect_decode);
			//ログイン失敗
			else:
				$user_model::add_user_log($account, $password, false);
				\Session::set_flash( 'error', 'ログインに失敗しました。入力内容に誤りがあります。');
				\Response::redirect('user/login/');
			endif;
		endif;

		//view
		$view = \View::forge('login');
		$view->set('ret', $redirect);
		$view->set_global('title', 'ログイン');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_logout()
	 */
	public function action_logout()
	{
		//session
		$session = \Session::instance();
		$session->delete('user');
		\Session::set_flash( 'success', 'ログアウトしました');
		\Response::redirect('user/login/');
	}
}
