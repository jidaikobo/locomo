<?php
namespace Locomo;
trait Model_Traits_Wrkflw
{
	protected static $_default_workflow_field_name   = 'workflow_status';

	/**
	 * get_current_step()
	*/
	public static function get_current_step($controller = null, $controller_id = null)
	{
		if (is_null($controller) || is_null($controller_id)) return false;

		// コントローラとidから最新のlcm_wrkflw_logs取得
		$q = \DB::select('id','workflow_id','current_step');
		$q->from('lcm_wrkflw_logs');
		$q->where('controller', \Inflector::add_head_backslash($controller));
		$q->where('controller_id', $controller_id);
		$q->order_by('created_at', 'DESC');
		$log = $q->execute()->current();

		// logが存在していない場合は経路設定前コンテンツなので、-2を返す
		return ($log) ? intval($log['current_step']) : -2;
	}

	/**
	 * get_current_step_id()
	*/
	public static function get_current_step_id($workflow_id = null, $step = null)
	{
		if (is_null($workflow_id) || is_null($step)) return false;

		// workflow_idとstepからstep_idを取得
		$q = \DB::select('id');
		$q->from('lcm_wrkflw_steps');
		$q->where('workflow_id', $workflow_id);
		$q->order_by('seq', 'ASC');
		$steps = $q->execute()->as_array();

		return (isset($steps[$step])) ? (int) $steps[$step]['id'] : false;
	}

	/**
	 * set_route()
	 * 経路設定。
	*/
	public static function set_route($route_id = null, $controller = null, $controller_id = null)
	{
		if (is_null($route_id) || is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());
		self::add_log($status = 'init', $route_id, $controller, $controller_id);
	}

	/**
	 * get_route()
	*/
	public static function get_route($controller = null, $controller_id = null)
	{
		if (is_null($controller) || is_null($controller_id)) return false;

		// 現在のワークフローidを得る
		$q = \DB::select('workflow_id');
		$q->from('lcm_wrkflw_logs');
		$q->where('controller', \Inflector::add_head_backslash($controller));
		$q->where('controller_id', $controller_id);
		$q->order_by('created_at', 'DESC');
		$q->limit(1);
		$id = $q->execute()->current();

		return ($id) ? (int) $id['workflow_id'] : false;
	}

	/**
	 * get_log_id()
	*/
	public static function get_log_id($controller = null, $controller_id = null)
	{
		if (is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());

		// ログidを得る
		$q = \DB::select('id');
		$q->from('lcm_wrkflw_logs');
		$q->where('controller', \Inflector::add_head_backslash($controller));
		$q->where('controller_id', $controller_id);
		$id = $q->execute()->current();

		return ($id) ? (int) $id['id'] : false;
	}

	/**
	 * get_total_step()
	*/
	public static function get_total_step($workflow_id = null)
	{
		if (is_null($workflow_id)) return false;

		// ワークフローの全体のステップを取得
		$q = \DB::select(\DB::expr('count(id)'));
		$q->from('lcm_wrkflw_steps');
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
		if (is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());
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
		if (is_null($workflow_id)) \Response::redirect(\Uri::base());

		// このルートに存在するすべてのユーザの取得
		$q = \DB::select('lcm_wrkflw_allowers.user_id');
		$q->from('lcm_wrkflw_allowers');
		$q->join('lcm_wrkflw_steps');
		$q->on('lcm_wrkflw_steps.id', '=', 'lcm_wrkflw_allowers.step_id');
		$q->where('workflow_id', $workflow_id);
		$members = \Arr::flatten($q->execute()->as_array());

		return $members ? $members : false ;
	}

	/**
	 * get_members()
	*/
	public static function get_members($step_id = null)
	{
		if (is_null($step_id)) \Response::redirect(\Uri::base());

		$members = \Model_Wrkflwadmin::find_allowers($step_id);
		$all = \Arr::get($members, 'allusers') ?: array();

		// add addmins
		\Arr::set($all, '-1:user_id', -1);
		\Arr::set($all, '-2:user_id', -2);

		return $all;
	}

	/**
	 * get_route_logs()
	*/
	public static function get_route_logs($controller = null, $controller_id = null)
	{
		if (is_null($controller) || is_null($controller_id)) \Response::redirect(\Uri::base());

		// 差戻しも含めて、これまでのすべての経路情報を取得する
		// ここで名前もとると便利そうだけど、管理者（-1,-2）がとれないので我慢する
/*
		$q = \DB::select('lcm_wrkflw_logs.*','lcm_usrs.display_name');
		$q->from('lcm_wrkflw_logs');
		$q->join('lcm_usrs');
		$q->on('lcm_wrkflw_logs.did_user_id', '=', 'lcm_usrs.id');
		$q->where('controller', \Inflector::add_head_backslash($controller));
		$q->where('controller_id', $controller_id);
		$q->where('current_step', "<>", -1);// 経路設定をした人は、承認申請する人と同じなので、除外する
		$q->order_by('created_at', 'ASC');
		$logs = $q->as_object()->execute()->as_array();
*/
		$q = \DB::select('*');
		$q->from('lcm_wrkflw_logs');
		$q->where('controller', \Inflector::add_head_backslash($controller));
		$q->where('controller_id', $controller_id);
		$q->where('current_step', "<>", -1);// 経路設定をした人は、承認申請する人と同じなので、除外する
		$q->order_by('created_at', 'ASC');
		$logs = $q->as_object()->execute()->as_array();

		return $logs ? $logs : false ;
	}

	/**
	 * get_strait_route_logs()
	 * stepをkeyとした最短ルートを返す
	*/
	public static function get_strait_route_logs($controller = null, $controller_id = null)
	{
		$logs = static::get_route_logs($controller, $controller_id);
		if ( ! $logs) return array();
		$max = max(array_keys($logs));
		$current_step = $logs[$max]->current_step ?: 0 ;

		$retvals = array();
		foreach ($logs as $log)
		{
			if ($current_step < $log->current_step) continue;
			$retvals[$log->current_step] = $log;
		}

		return $retvals ;
	}

	/**
	 * get_related_current_items()
	*/
	public static function get_related_current_items($controller = null, $model = null)
	{
		if (is_null($controller) || is_null($model)) die('workflow error. to get related currentitems.');

		// get unfinished items
		$unfinished = $model::find('all', array(
			'where'=>array(array('workflow_status', '<>', 'finish')),
			'limit' => '256',
			'order_by' => array(array('created_at','DESC'))
		));

		// add current step
		foreach ($unfinished as $id => $v)
		{
			// get latest log
			$q = \DB::select('workflow_id', 'current_step', 'created_at');
			$q->from('lcm_wrkflw_logs');
			$q->where('controller', \Inflector::add_head_backslash($controller));
			$q->where('controller_id', $id);
			$q->where('status', '<>', 'finish');
			$q->order_by('id', 'desc');
			$q->limit(1);
			$workflow = $q->execute()->current();

			// null means unrouted items
//			if(is_null($workflow)){}

			// make code shorten
			$workflow_id  = $workflow['workflow_id'];
			$current_step = $workflow['current_step'];

			// find writers
			$writers = \Model_Wrkflwadmin::find_writers($workflow_id);
			$column = \Arr::get($model::get_field_by_role('creator_id'), 'lcm_field', 'creator_id');
			if (isset(static::properties()[$column]))
			{
				$writers['allusers'][] = $v->{$column};
			}

			// get latest step_id
			$current_step_id = static::get_current_step_id($workflow_id, $current_step);

			// set latest action date
			$unfinished[$id]->latest_action_date = $workflow['created_at'];

			//set users - related members are 'allowers'
			if($current_step_id)
			{
				$unfinished[$id]->workflow_users = self::get_members($current_step_id);
			} else {
				// null means before progress - related members are 'writers'
				$unfinished[$id]->workflow_users = $writers['allusers'];
			}

			// add step information
			$unfinished[$id]->workflow_step_status = '';
			if (is_null($current_step))
			{
				$unfinished[$id]->workflow_step_status = '経路設定前';
			}
			elseif ($current_step == -1)
			{
				$unfinished[$id]->workflow_step_status = '申請待ち';
			}
			elseif ($current_step >= 0)
			{
				$q = \DB::select('name');
				$q->from('lcm_wrkflw_steps');
				$q->where('id', $current_step_id);
				$step_name = $q->execute()->current();

				$unfinished[$id]->workflow_step_status = $step_name['name'].' ('.$current_step.'/'.self::get_total_step($workflow_id).')';
			}

			// add apply date
			$q = \DB::select('created_at');
			$q->from('lcm_wrkflw_logs');
			$q->where('controller', \Inflector::add_head_backslash($controller));
			$q->where('controller_id', $id);
			$q->where('status', '<>', 'finish');
			$q->order_by('id', 'asc');
			$q->limit(1);
			$created_at = $q->execute()->current();

			$unfinished[$id]->workflow_apply_date = $created_at ? $created_at['created_at'] : '';
		}

		return $unfinished;
	}

	/**
	 * add_log()
	*/
	public static function add_log($status = 'approve', $workflow_id = null, $controller = null, $controller_id = null, $comment = '', $target_step = null)
	{
		if (is_null($controller) || is_null($controller_id) ) \Response::redirect(\Uri::base());

		// add_head_backslash
		$controller = \Inflector::add_head_backslash($controller);

		// workflow_idと現在のステップを取得
		$workflow_id = $workflow_id ? $workflow_id : self::get_route($controller, $controller_id);
		$current_step = self::get_current_step($controller, $controller_id);

		// current stepの変更
		if ($status == 'init'):
			$current_step = -1;
		elseif ($status == 'approve' || $status == 'finish'):
			$current_step++;
		elseif ($status == 'reject'):
			$current_step = -3;
		elseif ($status == 'remand'):
			$current_step = $target_step ? $target_step : $current_step - 1;
		endif;

		// 値の準備
		$set = array(
			'workflow_id'   => $workflow_id,
			'controller'    => $controller,
			'controller_id' => $controller_id,
			'current_step'  => $current_step,
			'status'        => $status,
			'comment'       => $comment,
			'created_at'    => date('Y-m-d H:i:s'),
			'did_user_id'   => \Auth::get('id'),
		);

		// ログのアップデート
		$q = \DB::insert();
		$q->table('lcm_wrkflw_logs');
		$q->set($set);
		$q->execute();

		// ログのidを取得
		$q = \DB::select(\DB::Expr('last_insert_id()'));
		$q->from('lcm_wrkflw_logs');
		$last_insert_id = $q->execute()->current();
		$log_id = $last_insert_id['last_insert_id()'];

		// 「次のユーザたち」をいったん削除
		$q = \DB::delete();
		$q->table('lcm_wrkflw_current_users');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		$q->execute();

		// 最後の承認か、ルート設定直後だったら、「次のユーザたち」を削除後return
		if ($status == 'finish' || $status == 'init'):
			return;
		endif;

		// 現在のステップのidを取得
		$step_id = self::get_current_step_id($workflow_id, $current_step);

		// 次のステップのユーザたちを取得
		$members = self::get_members($step_id);

		foreach($members as $user_id):
			$set = array(
				'log_id'        => $log_id,
				'controller'    => $controller,
				'controller_id' => $controller_id,
				'user_id'       => $user_id,
			);
			$q = \DB::insert();
			$q->table('lcm_wrkflw_current_users');
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
		if ( ! in_array('auth_workflow', static::$_authorize_methods)):
			static::$_authorize_methods[] = 'auth_workflow';
		endif;
	}

	/*
	 * auth_workflow()
	 * \Model_Base::add_authorize_methods()から呼ばれる
	 */
	public static function auth_workflow($controller = null, $options = array(), $mode = null)
	{
		// workflow_statusカラムがなければ、対象にしない
		$column = \Arr::get(static::get_field_by_role('workflow'), 'lcm_field', 'workflow_status');
		if ( ! isset(static::properties()[$column])) return $options;

		// 一覧にはfinish以外表示しない
		if ($mode == 'index')
		{
			$options['where'][] = array(array($column, '=', 'finish'));
		}

		// 作成ユーザと管理者はどんな条件でも閲覧できる - いったん一番上に
		if (\Auth::has_access($controller.'::action_create'))
		{
			return $options;
		}

		// 編集
		if ($mode == 'edit')
		{
			// 作成権限があるユーザだったらin_progress以外を編集できる
			if (\Auth::has_access($controller.'::action_create')):
				$options['where'][] = array(array($column, '<>', 'in_progress'));
				return $options;
			endif;
		}

		// 承認のための閲覧
		if (\Auth::has_access($controller.'::action_approve'))
		{
			// 承認ユーザはin_progressとfinishを閲覧できる
			$options['where'][] = array(array($column, 'IN', ['in_progress','finish']));
			return $options;
		}

		// 閲覧ユーザはfinishを閲覧できる
//		if (\Auth::has_access($controller.'::action_view'))
		if (\Auth::check())
		{
			$options['where'][] = array(array($column, '=', 'finish'));
			return $options;
		}

		// 一般ユーザは閲覧できない
		$pk = static::get_primary_keys('first');
		$options['where'][] = array(array($pk, '=', 'null'));
		return $options;
	}
}
