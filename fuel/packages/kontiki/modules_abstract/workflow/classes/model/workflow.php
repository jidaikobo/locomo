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
		if(is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::create($this->request->module.'/index_admin'));

		//コントローラとidからworkflow_logs取得
		$q = \DB::select('id','workflow_id','current_step');
		$q->from('workflow_logs');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		$log = $q->execute()->current();

		//logが存在していない場合は申請前コンテンツなので、-1/Nを返す
		$step = '-1/N';

		//logが存在していたら、全体のステップ数（N）を確認し、current_step/Nを返す
		if($log):
			$total_step = self::get_total_step($log['workflow_id']);
			$step = $log['current_step'].'/'.$total_step;
		endif;

		return $step;
	}

	/**
	 * set_route()
	 * 経路設定。
	*/
	public static function set_route($route_id = null, $controller = null, $controller_id = null)
	{
		if(is_null($route_id) || is_null($controller) || is_null($controller_id))
			\Response::redirect(\Uri::create($this->request->module.'/index_admin'));

		//値の準備
		$set = array(
			'workflow_id'   => $route_id,
			'controller'    => $controller,
			'controller_id' => $controller_id,
			'current_step'  => '-1',
			'status'        => '',
			'comment'       => '',
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
	 * get_route()
	*/
	public static function get_route($controller = null, $controller_id = null)
	{
		if(is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::create($this->request->module.'/index_admin'));

		//ワークフローの全体のステップを取得
		$q = \DB::select('id');
		$q->from('workflow_logs');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		$id = $q->execute()->current();

		return $id;
	}

	/**
	 * get_total_step()
	*/
	public static function get_total_step($workflow_id = null)
	{
		if(is_null($workflow_id)) \Response::redirect(\Uri::create($this->request->module.'/index_admin'));

		//ワークフローの全体のステップを取得
		$q = \DB::select(\DB::expr('count(id)'));
		$q->from('workflow_steps');
		$q->where('workflow_id', $workflow_id);
		$count = $q->execute()->current();

		return $count ? $count['count(id)'] : false ;
	}

	/**
	 * increase_step()
	*/
	public static function increase_step($current_step = null, $log_id = null)
	{
		if(is_null($log_id)) \Response::redirect(\Uri::create($this->request->module.'/index_admin'));
		//current_stepを加算

	}

	/**
	 * decrease_step()
	*/
	public static function decrease_step($current_step = 0, $log_id = null)
	{
		if(is_null($log_id)) \Response::redirect(\Uri::create($this->request->module.'/index_admin'));
		//current_stepが0だったらそのまま

		//current_stepを減算

	}

	/**
	 * find_workflow_setting()
	*/
	public static function find_workflow_setting($id = null)
	{
		if(is_null($id)) \Response::redirect(\Uri::create($this->request->module.'/index_admin'));

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
		if(is_null($workflow_id) || is_null($args)) \Response::redirect(\Uri::create($this->request->module.'/index_admin'));

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