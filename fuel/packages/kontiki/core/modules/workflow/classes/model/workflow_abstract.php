<?php
namespace Kontiki;
abstract class Model_Workflow extends \Kontiki\Model
{
	protected static $_table_name = 'workflows';

	/**
	 * get_current_step()
	*/
	public static function get_current_step($controller = null, $controller_id = null)
	{
		if(is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());

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
		if(is_null($workflow_id) || is_null($step)) \Response::redirect(\Uri::base());

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
		if(is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());

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
		if(is_null($workflow_id)) \Response::redirect(\Uri::base());

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
		elseif($status == 'approve'):
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
			'creator_id'    => \User\Controller_User::$userinfo['user_id'],
		);

		//insert
		$q = \DB::insert();
		$q->table('workflow_logs');
		$q->set($set);
		$q->execute();
	}

}