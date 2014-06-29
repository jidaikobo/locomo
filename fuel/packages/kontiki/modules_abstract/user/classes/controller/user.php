<?php
namespace Kontiki;

abstract class Controller_User_Abstract extends \Kontiki\Controller
{
	/**
	* @var string name for human
	*/
	public static $nicename = 'ユーザ管理';


	/**
	 * messages
	 * 
	 */
	protected $messages = array(
		'auth_error'       => 'You are not permitted.',
		'view_error'       => 'ユーザID %2$d は見つかりませんでした。',
		'create_success'   => 'ユーザID %2$d を新規作成しました。',
		'create_error'     => 'Could not save %s.',
		'edit_success'     => 'Updated %s #%d.',
		'edit_error'       => 'Could not update %s #%d.',
		'delete_success'   => 'Deleted %s #%d.',
		'delete_error'     => 'Could not delete %s #%d.',
		'undelete_success' => 'Undeleted %s #%d.',
		'undelete_error'   => 'Could not undelete %s #%d.',
		'purge_success'    => 'Completely deleted %s #%d.',
		'purge_error'      => 'Could not delete %s #%d.',
	);
	protected $titles = array(
		'index'          => '%1$s.',
		'view'           => '%1$s.',
		'create'         => 'Create %1$s.',
		'edit'           => 'Edit %1$s.',
		'index_deleted'  => 'Delete List %1$s.',
		'index_yet'      => 'Yet List %1$s.',
		'index_expired'  => 'Expired List %1$s.',
		'view_deleted'   => 'Deleted %1$s.',
		'edit_deleted'   => 'Edit Deleted %1$s.',
		'confirm_delete' => 'Are you sure to Permanently Delete a %1$s?',
		'delete_deleted' => 'Completely Delete a %1$s.',
	);

	/**
	 * test datas
	 * 
	 */
	protected $test_datas = array(
		'user_name' => 'text',
		'password'  => 'text',
		'email'     => 'email',
		'status'    => 'int',
	);

	/**
	 * view_hook()
	 * 
	 */
	public function view_hook($obj = NULL, $mode = 'edit')
	{
		//このシステムのすべてのユーザグループ（選択肢用）
		$usergroups = \Usergroup\Model_Usergroup::find('all');
		$view = \View::forge('edit');
		$view->set_global('usergroups', $usergroups, false);

		//現在のユーザが所属するグループ
		$user_id = intval($obj->id);
		$sql = "SELECT `usergroup_id` FROM users_usergroups_r WHERE `user_id` = {$user_id}" ;
		$resuls = \DB::query($sql)->execute()->as_array();
		$obj->usergroups = $resuls ? \Arr::flatten_assoc($resuls) : array();

		return $obj;
	}

	/**
	 * post_save_hook()
	 * 
	 */
	public function post_save_hook($obj = NULL, $mode = 'edit')
	{
		//ユーザが所属するグループを更新
		if (\Input::method() == 'POST'):
			$user_id = intval($obj->id);
			//まずすべて削除
			$sql = 'DELETE FROM users_usergroups_r WHERE user_id = '.$user_id;
			\DB::query($sql)->execute();

			//ユーザグループを更新
			if(is_array(\Input::post('usergroup'))):
				foreach(\Input::post('usergroup') as $group_id => $v):
					$group_id = intval($group_id);
					$sql = "INSERT INTO users_usergroups_r (user_id,usergroup_id) VALUES ('{$user_id}','{$group_id}')";
					\DB::query($sql)->execute();
				endforeach;
			endif;
		endif;
		return $obj;
	}

	/**
	 * hash()
	 * 
	 */
	private static function hash($str)
	{
		return md5($str);
	}

	/**
	 * action_login()
	 * 
	 */
	public function action_login($redirect = NULL)
	{
		//ログイン処理
		if(\Input::method() == 'POST'):
			$account = \Input::post('account');
			$password = \Input::post('password');
			if($account == null || $password == null):
				\Session::set_flash( 'error', 'ユーザ名とパスワードの両方を入力してください');
				\Response::redirect('user/login/');
			endif;

			//確認
			$q = \DB::select('id');
			$q->from('users');
			$q->where('password', self::hash($password));
			$q->where('deleted_at', '=', null);
			$q->where('created_at', '<=', date('Y-m-d H:i:s'));
			$q->where('expired_at', '>=', date('Y-m-d H:i:s'));
			$q->and_where_open();
				$q->where('user_name', '=', $account);
				$q->or_where('email', '=', $account);
			$q->and_where_close();
			$user_ids = $q->execute()->current() ;
			$user_id = $user_ids['id'] ;

			//ログイン成功
			if($user_id):
				//get usergroup ids
				$q = \DB::select('users_usergroups_r.usergroup_id');
				$q->distinct();
				$q->from('usergroups');
				$q->from('users_usergroups_r');
				$q->where('users_usergroups_r.user_id', '=', $user_id);
				$q->where('usergroups.deleted_at', '=', null);
				$q->where('usergroups.deleted_at', '=', null);
				$q->where('usergroups.created_at', '<=', date('Y-m-d H:i:s'));
				$q->where('usergroups.expired_at', '>=', date('Y-m-d H:i:s'));
				$usergroup_ids = \Arr::flatten_assoc($q->execute()->as_array()) ;

				//session
				$session = \Session::instance();
				$session->set('user', array('user_id' => $user_id, 'usergroup_ids' => $usergroup_ids));

				//redirect
				\Session::set_flash( 'success', 'ログインしました。');
				\Response::redirect('user/');
			//ログイン失敗
			else:
				\Session::set_flash( 'error', 'ログインに失敗しました。入力内容に誤りがあります。');
				\Response::redirect('user/login/');
			endif;
		endif;

/*
echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
var_dump( \Session::get('user') ) ;
echo '</textarea>' ;
die();
*/

		//view
		$view = \View::forge('login');
		$view->set('hidden_ret', $redirect);
		$view->set_global('title', 'ログイン');
		return \Response::forge($view);
	}

}
