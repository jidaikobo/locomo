<?php
namespace Locomo;
class Model_Wrkflwadmin extends \Model_Base
{
	protected static $_table_name = 'lcm_wrkflws';

	protected static $_properties = array(
		'id',
		'name' => array(
			'label' => 'ワークフロー名',
			'form' => array('type' => 'text', 'class' => 'text'),
			'validation' => array(
				'required',
			),
		),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
	);

	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	/**
	 * form_definition()
	*/
	public static function form_definition($factory = 'user', $obj = null)
	{
		$id = isset($obj->id) ? $obj->id : '';

		//forge
		$form = parent::form_definition($factory, $obj);

		//name
		$form->field('name')->add_rule('unique', "lcm_wrkflws.name.{$id}");

		return $form;
	}

	/**
	 * search_form()
	*/
	public static function search_form($factory = 'wkflwadmin', $obj = null, $title = '')
	{
		$form = parent::search_form($factory, $obj, 'ワークフロー一覧');

		$form
			->add_after('all', '検索', array('type' => 'text','value' => \Input::get('all')), array(), 'opener');

		return $form;
	}


	/**
	 * find_workflow_setting()
	*/
	public static function find_workflow_setting($workflow_id = null)
	{
		if (is_null($workflow_id)) \Response::redirect(\Uri::base());

		// retvals
		$retvals = array();

		// workflow_steps
		$q = \DB::select('*');
		$q->from('lcm_wrkflw_steps');
		$q->where('workflow_id', $workflow_id);
		$q->order_by('seq', 'ASC');
		$steps = $q->execute()->as_array();

		//find_writers
		$retvals['writers'] = self::find_writers($workflow_id);

		// allowers
		$n = 1;
		foreach($steps as $k => $step)
		{
			$retvals['allowers'][$n] = $step;
			$step_id = $step['id'];

			//find_writers
			$retvals['allowers'][$n] = array_merge($retvals['allowers'][$n], self::find_allowers($step['id']));

			$n++;
		}

		return $retvals;
	}

	/**
	 * find_writers()
	*/
	public static function find_writers($workflow_id = null)
	{
		$retvals = array();

		// writers - user
		$q = \DB::select('user_id');
		$q->from('lcm_wrkflw_allowers');
		$q->where('workflow_id', $workflow_id);
		$q->where('is_writer', true);
		$q->where('user_id', '<>', 0);
		$user_ids = $q->execute()->as_array();
		$user_ids = \Arr::flatten($user_ids);
		$retvals['users'] = join(',', $user_ids);

		// writers - usergroup
		$q = \DB::select('usergroup_id');
		$q->from('lcm_wrkflw_allowers');
		$q->where('workflow_id', $workflow_id);
		$q->where('is_writer', true);
		$q->where('usergroup_id', '<>', 0);
		$usergroups = $q->execute()->as_array();
		$usergroups = \Arr::flatten($usergroups);
		$retvals['groups'] = join(',', $usergroups);;

		// writers - user_id from usergroup - not for edit page
		$q = \DB::select('lcm_usrs_usrgrps.user_id');
		$q->from('lcm_wrkflw_allowers');
		$q->join('lcm_usrs_usrgrps');
		$q->on('lcm_usrs_usrgrps.group_id', '=', 'lcm_wrkflw_allowers.usergroup_id');
		$q->where('lcm_wrkflw_allowers.workflow_id', $workflow_id);
		$q->where('lcm_wrkflw_allowers.is_writer', true);
		$writers = $q->execute()->as_array();
		$writers = \Arr::flatten($writers);
		$retvals['allusers'] = $user_ids + $writers;

		return $retvals;
	}

	/**
	 * find_allowers()
	*/
	public static function find_allowers($step_id = null)
	{
		$retvals = array();

		// users
		$q = \DB::select('user_id');
		$q->from('lcm_wrkflw_allowers');
		$q->where('step_id', $step_id);
		$q->where('is_writer', false);
		$q->where('user_id', '<>', 0);
		$user_ids = $q->execute()->as_array();
		$user_ids = \Arr::flatten($user_ids);
		$allower_str = join(',',$user_ids);
		$retvals['users'] = $allower_str;

		// usergroup_id
		$q = \DB::select('usergroup_id');
		$q->from('lcm_wrkflw_allowers');
		$q->where('step_id', $step_id);
		$q->where('is_writer', false);
		$q->where('usergroup_id', '<>', 0);
		$allowers = $q->execute()->as_array();
		$allowers = \Arr::flatten($allowers);
		$allower_str = join(',',$allowers);
		$retvals['groups'] = $allower_str;

		// allowers - user_id from usergroup - not for edit page
		$q = \DB::select('lcm_usrs_usrgrps.user_id');
		$q->from('lcm_wrkflw_allowers');
		$q->join('lcm_usrs_usrgrps');
		$q->on('lcm_usrs_usrgrps.group_id', '=', 'lcm_wrkflw_allowers.usergroup_id');
		$q->where('lcm_wrkflw_allowers.step_id', '=', $step_id);
		$q->where('lcm_wrkflw_allowers.is_writer', false);
		$allowers = $q->execute()->as_array();
		$allowers = \Arr::flatten($allowers);
		$retvals['allusers'] = $user_ids + $allowers;

		return $retvals;
	}

	/**
	 * update_workflow_setting()
	*/
	public static function update_workflow_setting($workflow_id = null, $args = null)
	{
		if (is_null($workflow_id) || is_null($args)) \Response::redirect(\Uri::base());

		// initialize workflow_steps
		$q = \DB::delete();
		$q->table('lcm_wrkflw_steps');
		$q->where('workflow_id', $workflow_id);
		$q->execute();

		// initialize lcm_wrkflw_allowers
		$q = \DB::delete();
		$q->table('lcm_wrkflw_allowers');
		$q->where('workflow_id', $workflow_id);
		$q->execute();

		//loop
		foreach($args['allowers'] as $order => $arg)
		{
			// step_id from lcm_wrkflw_steps
			$q = \DB::select('id');
			$q->from('lcm_wrkflw_steps');
			$q->where('workflow_id', $workflow_id);
			$q->where('seq', $order);
			$step_id = $q->execute()->current();

			// prepare allowers
			$set = array(
				'name'        => $arg['name'],
				'workflow_id' => $workflow_id,
				'condition'   => $arg['condition'],
				'seq'         => $order,
				'action'      => $arg['action'],
				'is_writer'   => false,
			);

			if ($step_id)
			{
				// update
				$q = \DB::update();
				$q->table('lcm_wrkflw_steps');
				$q->set($set);
				$q->where('id', $step_id['id']);
				$q->execute();
			}
			else
			{
				// insert
				$q = \DB::insert();
				$q->table('lcm_wrkflw_steps');
				$q->set($set);
				$q->execute();

				// id
				$q = \DB::select(\DB::Expr('last_insert_id()'));
				$q->from('lcm_wrkflw_steps');
				$last_insert_id = $q->execute()->current();
				$step_id['id'] = $last_insert_id['last_insert_id()'];
			}

			if($arg['users'])
			{
				foreach(explode(',',$arg['users']) as $allower)
				{
					$allower = intval($allower);
					if( ! $allower) continue;
					// preparation
					$set = array(
						'workflow_id'  => $workflow_id,
						'step_id'      => $step_id['id'],
						'user_id'      => $allower,
						'is_writer'    => false,
					);
					// insert
					$q = \DB::insert();
					$q->table('lcm_wrkflw_allowers');
					$q->set($set);
					$q->execute();
				}
			}

			if($arg['groups'])
			{
				foreach(explode(',',$arg['groups']) as $allower)
				{
					$allower = intval($allower);
					if( ! $allower) continue;
					// preparation
					$set = array(
						'workflow_id'  => $workflow_id,
						'step_id'      => $step_id['id'],
						'usergroup_id' => $allower,
						'is_writer'    => false,
					);
					// insert
					$q = \DB::insert();
					$q->table('lcm_wrkflw_allowers');
					$q->set($set);
					$q->execute();
				}
			}
		}

		// update writers - group
		if($args['writers']['groups'])
		{
			foreach(explode(',',$args['writers']['groups']) as $writer_group)
			{
				$writer_group = intval($writer_group);
				if( ! $writer_group) continue;
				// preparation
				$set = array(
					'workflow_id'  => $workflow_id,
					'step_id'      => 0,
					'usergroup_id' => $writer_group,
					'is_writer'    => true,
				);
				// insert
				$q = \DB::insert();
				$q->table('lcm_wrkflw_allowers');
				$q->set($set);
				$q->execute();
			}
		}

		// update writers - user
		if($args['writers']['users'])
		{
			foreach(explode(',',$args['writers']['users']) as $writer_users)
			{
				$writer_users = intval($writer_users);
				if( ! $writer_users) continue;
				// preparation
				$set = array(
					'workflow_id' => $workflow_id,
					'step_id'     => 0,
					'user_id'     => $writer_users,
					'is_writer'   => true,
				);
				// insert
				$q = \DB::insert();
				$q->table('lcm_wrkflw_allowers');
				$q->set($set);
				$q->execute();
			}
		}

		return true;
	}
}