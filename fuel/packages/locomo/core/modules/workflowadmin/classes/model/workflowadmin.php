<?php
namespace Workflowadmin;
class Model_Workflowadmin extends \Locomo\Model_Base
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
	 * form_definition()
	*/
	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory, $obj = null, $id = '')
	{
		if(static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;

		//forge
		$form = \Fieldset::forge('form', \Config::get('form'));

		//user_name
		$form->add(
				'name',
				'ルート名名',
				array('type' => 'text', 'size' => 255)
			)
			->set_value(@$obj->name)
			->add_rule('required')
			->add_rule('max_length', 255)
			->add_rule('unique', "workflows.name.{$id}");

		static::$_cache_form_definition = $form;
		return $form;
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
				'action'      => $arg['action'],
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