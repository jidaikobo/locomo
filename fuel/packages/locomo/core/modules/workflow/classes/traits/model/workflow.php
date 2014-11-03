<?php
namespace Workflow;
trait Traits_Model_Workflow
{
	protected static $_default_workflow_field_name   = 'workflow_status';

	/**
	 * get_default_field_name($str)
	 */
	public static function get_default_field_name($str = null)
	{
		switch($str):
			case 'workflow':
				return static::$_default_workflow_field_name;
		endswitch;
		return parent::get_default_field_name($str);
	}

	/**
	 * get_current_step()
	*/
	public static function get_current_step($controller = null, $controller_id = null)
	{
		if(is_null($controller) || is_null($controller_id)) return false;

		//コントローラとidから最新のworkflow_logs取得
		$q = \DB::select('id','workflow_id','current_step');
		$q->from('workflow_logs');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		$q->order_by('created_at', 'DESC');
		$log = $q->execute()->current();

		//logが存在していない場合は経路設定前コンテンツなので、-2を返す
		return ($log) ? intval($log['current_step']) : -2;
	}

	/**
	 * get_current_step_id()
	*/
	public static function get_current_step_id($workflow_id = null, $step = null)
	{
		if(is_null($workflow_id) || is_null($step)) return false;

		//workflow_idとstepからstep_idを取得
		$q = \DB::select('id');
		$q->from('workflow_steps');
		$q->where('workflow_id', $workflow_id);
		$q->order_by('order', 'ASC');
		$steps = $q->execute()->as_array();

		return (isset($steps[$step])) ? (int) $steps[$step]['id'] : false;
	}

	/**
	 * set_route()
	 * 経路設定。
	*/
	public static function set_route($route_id = null, $controller = null, $controller_id = null)
	{
		if(is_null($route_id) || is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());
		self::add_log($status = 'init', $route_id, $controller, $controller_id);
	}

	/**
	 * get_route()
	*/
	public static function get_route($controller = null, $controller_id = null)
	{
		if(is_null($controller) || is_null($controller_id)) return false;

		//ワークフローidを得る
		$q = \DB::select('workflow_id');
		$q->from('workflow_logs');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		$id = $q->execute()->current();

		return ($id) ? (int) $id['workflow_id'] : false;
	}

	/**
	 * get_log_id()
	*/
	public static function get_log_id($controller = null, $controller_id = null)
	{
		if(is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());

		//ログidを得る
		$q = \DB::select('id');
		$q->from('workflow_logs');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		$id = $q->execute()->current();

		return ($id) ? (int) $id['id'] : false;
	}

	/**
	 * get_total_step()
	*/
	public static function get_total_step($workflow_id = null)
	{
		if(is_null($workflow_id)) return false;

		//ワークフローの全体のステップを取得
		$q = \DB::select(\DB::expr('count(id)'));
		$q->from('workflow_steps');
		$q->where('workflow_id', $workflow_id);
		$count = $q->execute()->current();

		return $count ? (int) $count['count(id)'] : false ;
	}

	/**
	 * is_in_workflow()
	 * 項目がすべて承認済みかどうかを確認する
	*/
	public static function is_in_workflow($controller = null, $controller_id = null)
	{
		if(is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());
		$workflow_id = self::get_route($controller, $controller_id);
		$current_step = self::get_current_step($controller, $controller_id);
		$total_step = self::get_total_step($workflow_id);
		return $current_step < $total_step ? true : false;
	}

	/**
	 * get_all_members()
	*/
	public static function get_all_members($workflow_id = null)
	{
		if(is_null($workflow_id)) \Response::redirect(\Uri::base());

		//このルートに存在するすべてのユーザの取得
		$q = \DB::select('workflow_allowers.user_id');
		$q->from('workflow_allowers');
		$q->join('workflow_steps');
		$q->on('workflow_steps.id', '=', 'workflow_allowers.step_id');
		$q->where('workflow_id', $workflow_id);
		$members = \Arr::flatten($q->execute()->as_array());

		return $members ? $members : false ;
	}

	/**
	 * get_members()
	*/
	public static function get_members($workflow_id = null, $step_id = null)
	{
		if(is_null($workflow_id) || is_null($step_id)) \Response::redirect(\Uri::base());

		//当該ステップのメンバー
		$q = \DB::select('user_id');
		$q->from('workflow_allowers');
		$q->where('step_id', $step_id);
		$members = \Arr::flatten($q->execute()->as_array());

		return $members ? $members : false ;
	}

	/**
	 * get_route_logs()
	*/
	public static function get_route_logs($controller = null, $controller_id = null, $step = null)
	{
		if(is_null($controller) || is_null($controller_id) || is_null($step)) \Response::redirect(\Uri::base());

		//差戻しも含めて、これまでのすべての経路情報を取得する
		$q = \DB::select('*');
		$q->from('workflow_logs');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		$q->where('current_step', "<>", -1);//経路設定をした人は、承認申請する人と同じなので、除外する
		$q->order_by('created_at', 'ASC');
		$members = $q->as_object()->execute()->as_array();

		return $members ? $members : false ;
	}

	/**
	 * get_related_current_available_items()
	*/
	public static function get_related_current_available_items($controller = null, $userinfo = null)
	{
		$userinfo = $userinfo ? $userinfo : \Auth::get_userinfo();

		//ユーザがいまアクションできる項目のコントローラとidを得る
		$q = \DB::select('*');
		$q->from('workflow_current_users');
		$q->where('user_id', $userinfo['user_id']);
		if($controller) $q->where('controller', $controller);
		$q->limit('256');
		$availables = $q->as_object()->execute()->as_array();

		return $availables;
	}

	/**
	 * get_related_current_items()
	*/
	public static function get_related_current_items($controller = null, $userinfo = null)
	{
		$userinfo = $userinfo ? $userinfo : \Auth::get_userinfo();

		//ユーザidからworkflow_idを取得
		$q = \DB::select('workflow_steps.workflow_id');
		$q->from('workflow_steps');
		$q->join('workflow_allowers');
		$q->on('workflow_steps.id', '=', 'workflow_allowers.step_id');
		$q->where('workflow_allowers.user_id', $userinfo['user_id']);
		$workflow_ids = \Arr::flatten($q->execute()->as_array());

		//workflow_idから進行中のコントローラとidを得る
		$retvals = array();
		$n = 0;
		foreach($workflow_ids as $workflow_id):
			$q = \DB::select('workflow_logs.controller','workflow_logs.controller_id');
			$q->distinct();
			$q->from('workflow_logs');
			$q->where('workflow_logs.workflow_id', $workflow_id);
			$q->where('workflow_logs.status', '<>', 'finish');
			$q->limit('256');
			$ctrls = $q->execute()->as_array();
			if( ! $ctrls) continue;
				foreach($ctrls as $ctrl):
				$retvals[$n] = (object) array();
				$retvals[$n]->controller    = $ctrl['controller'];
				$retvals[$n]->controller_id = $ctrl['controller_id'];
				$n++;
			endforeach;
		endforeach;

		return $retvals;
	}

	/**
	 * add_log()
	*/
	public static function add_log($status = 'approve', $workflow_id = null, $controller = null, $controller_id = null, $comment = '', $target_step = null)
	{
		if(is_null($controller) || is_null($controller_id) ) \Response::redirect(\Uri::base());

		//workflow_idと現在のステップを取得
		$workflow_id = $workflow_id ? $workflow_id : self::get_route($controller, $controller_id);
		$current_step = self::get_current_step($controller, $controller_id);

		//current stepの変更
		if($status == 'init'):
			$current_step = -1;
		elseif($status == 'approve' || $status == 'finish'):
			$current_step++;
		elseif($status == 'reject'):
			$current_step = -3;
		elseif($status == 'remand'):
			$current_step = $target_step ? $target_step : $current_step - 1;
		endif;

		//値の準備
		$set = array(
			'workflow_id'   => $workflow_id,
			'controller'    => $controller,
			'controller_id' => $controller_id,
			'current_step'  => $current_step,
			'status'        => $status,
			'comment'       => $comment,
			'created_at'    => date('Y-m-d H:i:s'),
			'did_user_id'   => \Auth::get_user_id(),
		);

		//ログのアップデート
		$q = \DB::insert();
		$q->table('workflow_logs');
		$q->set($set);
		$q->execute();

		//ログのidを取得
		$q = \DB::select(\DB::Expr('last_insert_id()'));
		$q->from('workflow_logs');
		$last_insert_id = $q->execute()->current();
		$log_id = $last_insert_id['last_insert_id()'];

		//「次のユーザたち」をいったん削除
		$q = \DB::delete();
		$q->table('workflow_current_users');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		$q->execute();

		//最後の承認か、ルート設定直後だったら、「次のユーザたち」を削除後return
		if($status == 'finish' || $status == 'init'):
			return;
		endif;

		//現在のステップのidを取得
		$step_id = self::get_current_step_id($workflow_id, $current_step);

		//次のステップのユーザたちを取得
		$members = self::get_members($workflow_id, $step_id);

		foreach($members as $user_id):
			$set = array(
				'log_id'        => $log_id,
				'controller'    => $controller,
				'controller_id' => $controller_id,
				'user_id'       => $user_id,
			);
			$q = \DB::insert();
			$q->table('workflow_current_users');
			$q->set($set);
			$q->execute();
		endforeach;

		return;
	}


	/**
	 * add_authorize_methods()
	 */
	public static function add_authorize_methods()
	{
		if( ! in_array('auth_workflow', static::$_authorize_methods)):
			static::$_authorize_methods[] = 'auth_workflow';
		endif;
	}

	/*
	 * auth_workflow()
	 */
	public static function auth_workflow($controller = null, $userinfo = null, $options = array(), $mode = null)
	{
		//workflow_statusカラムがなければ、対象にしない
		$column = isset(static::$_workflow_field_name) ?
			static::$_workflow_field_name :
			static::get_default_field_name('workflow');
		if( ! isset(static::properties()[$column])) return $options;

		//編集
		if ($mode == 'edit') {
			//作成権限があるユーザだったらin_progress以外を編集できる
			if(\Auth::auth($controller.'/create', $userinfo)):
				$options['where'][] = array(array($column, '<>', 'in_progress'));
				return $options;
			endif;
		}

		//承認のための閲覧
		if(\Auth::auth($controller.'/approve', $userinfo)):
			//承認ユーザはin_progressとfinishを閲覧できる
			$options['where'][] = array(array($column, 'IN', ['in_progress','finish']));
			return $options;
		endif;

		//作成ユーザはどんな条件でも閲覧できる
		if(\Auth::auth($controller.'/create', $userinfo)):
			return $options;
		endif;

		//閲覧ユーザはfinishを閲覧できる
		if(\Auth::auth($controller.'/view', $userinfo)):
			$options['where'][] = array(array($column, '=', 'finish'));
			return $options;
		endif;

		//一般ユーザは閲覧できない
		$pk = static::get_primary_keys('first');
		$options['where'][] = array(array($pk, '=', 'null'));
		return $options;
	}

}