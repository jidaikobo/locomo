<?php
namespace Kontiki;
class Model_Workflow_Abstract extends \Kontiki\Model
{
	protected static $_table_name = 'workflows';

	protected static $_properties = array(
		'id',
		'name',
		'deleted_at',
	);

	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

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
		self::add_log($mode = 'init', $route_id, $controller, $controller_id);
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
	 * add_log()
	*/
	public static function add_log($mode = 'increase', $workflow_id = null, $controller = null, $controller_id = null, $comment = '')
	{
		if(is_null($controller) || is_null($controller_id) ) \Response::redirect(\Uri::base());

		//workflow_idと現在のステップを取得
		$workflow_id = $workflow_id ? $workflow_id : self::get_route($controller, $controller_id);
		$current_step = self::get_current_step($controller, $controller_id);

		//current stepの変更
		if($mode == 'init'):
			$current_step = -1;
		elseif($mode == 'increase'):
			$current_step++;
		elseif($mode == 'reject'):
			$current_step = -3;
		else:
			$current_step--;
		endif;

		//値の準備
		$set = array(
			'workflow_id'   => $workflow_id,
			'controller'    => $controller,
			'controller_id' => $controller_id,
			'current_step'  => $current_step,
			'status'        => '',
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

	/**
	 * decrease_step()
	*/
	public static function decrease_step($current_step = 0, $log_id = null)
	{
		if(is_null($log_id)) \Response::redirect(\Uri::base());
		//current_stepが0だったらそのまま

		//current_stepを減算

	}

	/**
	 * find_workflow_setting()
	*/
	public static function find_workflow_setting($id = null)
	{
		if(is_null($id)) \Response::redirect(\Uri::base());

		//workflow_steps取得
		$q = \DB::select('*');
		$q->from('workflow_steps');
		$q->where('workflow_id', $id);
		$q->order_by('order', 'ASC');
		$steps = $q->execute()->as_array();
		
		$retvals = array();
		$n = 1;
		foreach($steps as $k => $step):
			$retvals[$n] = $step;
			$step_id = $step['id'];

			//workflow_step_actions取得
			$q = \DB::select('action');
			$q->from('workflow_step_actions');
			$q->where('step_id', $step_id);
			$action = $q->execute()->current();
			$retvals[$n]['actions'] = $action['action'];

			//workflow_allowers取得
			$q = \DB::select('user_id');
			$q->from('workflow_allowers');
			$q->where('step_id', $step_id);
			$allowers = $q->execute()->as_array();
			$allowers = \Arr::flatten($allowers);
			$allower_str = join(',',$allowers);
			$retvals[$n]['allowers'] = $allower_str;

			$n++;
		endforeach;
		return $retvals;
	}

	/**
	 * update_workflow()
	*/
	public static function update_workflow($workflow_id = null, $args = null)
	{
		if(is_null($workflow_id) || is_null($args)) \Response::redirect(\Uri::base());

		//loop
		foreach($args as $order => $arg):
			//workflow_stepsテーブルの確認
			$q = \DB::select('id');
			$q->from('workflow_steps');
			$q->where('workflow_id', $workflow_id);
			$q->where('order', $order);
			$step_id = $q->execute()->current();

			//値の準備
			$set = array(
				'name'        => $arg['name'],
				'workflow_id' => $workflow_id,
				'condition'   => $arg['condition'],
				'order'       => $order,
			);

			if($step_id):
				//更新
				$q = \DB::update();
				$q->table('workflow_steps');
				$q->set($set);
				$q->where('id', $step_id['id']);
				$q->execute();
			else:
				//新規作成
				$q = \DB::insert();
				$q->table('workflow_steps');
				$q->set($set);
				$q->execute();

				//id
				$q = \DB::select(\DB::Expr('last_insert_id()'));
				$q->from('workflow_steps');
				$last_insert_id = $q->execute()->current();
				$step_id['id'] = $last_insert_id['last_insert_id()'];
			endif;

			//workflow_step_actionsテーブルの確認
			$q = \DB::select('id');
			$q->from('workflow_step_actions');
			$q->where('step_id', $step_id['id']);
			$actions_id = $q->execute()->current();

			//値の準備
			$set = array(
				'step_id' => $step_id['id'],
				'action'  => $arg['actions'],
			);
			if($actions_id):
				//更新
				$q = \DB::update();
				$q->table('workflow_step_actions');
				$q->set($set);
				$q->where('id', $actions_id['id']);
				$q->execute();
			else:
				//新規作成
				$q = \DB::insert();
				$q->table('workflow_step_actions');
				$q->set($set);
				$q->execute();
			endif;

			//workflow_allowersテーブルを初期化
			$q = \DB::delete();
			$q->table('workflow_allowers');
			$q->where('step_id', $step_id['id']);
			$q->execute();

			foreach(explode(',',$arg['allowers']) as $allower):
				$allower = intval($allower);
				//値の準備
				$set = array(
					'step_id' => $step_id['id'],
					'user_id' => $allower,
				);
				//新規作成
				$q = \DB::insert();
				$q->table('workflow_allowers');
				$q->set($set);
				$q->execute();
			endforeach;
		endforeach;

		return true;
	}

}