<?php
namespace Locomo;
trait Actionset_Traits_Wrkflw
{
	/**
	 * actionset_index_workflow()
	 */
	public static function actionset_index_workflow($controller, $obj = null, $id = null, $urls = array())
	{
		// count
		static $count;
		$model = str_replace('Controller', 'Model', $controller);
		if (class_exists($model) && ! $count)
		{
			$pk = $model::get_primary_keys('first');
			$options = array();
			$options[] = array($pk, 'is not' , null);
			if (isset($model::properties()['created_at']))
			{
				$options[] = array('created_at', '<=', date('Y-m-d H:i:s'));
			}
			if (isset($model::properties()['expired_at']))
			{
				$options[] = array('expired_at', 'is', null);
			}
			if (isset($model::properties()['is_visible']))
			{
				$options[] = array('is_visible', '=', true);
			}
			$count = count($model::get_related_current_items($controller, $model));
		}

		// urls
		$count = " ({$count})";
		$actions = array(array($controller.DS."index_workflow", "承認項目一覧{$count}"));
		$urls = static::generate_urls($controller.'::action_index_workflow', $actions);

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls,
			'action_name'  => 'ワークフロー承認項目一覧',
			'explanation'  => '現在承認すべき項目の一覧です。',
			'acl_exp'      => '現在承認すべき項目の一覧です。「ワークフロー作業」「ワークフロー承認」権限と同時に自動的に付与されます。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'::action_index_workflow',
			)
		);
		return $retvals;
	}

	/**
	 * workflow()
	 */
	public static function actionset_apply($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'action_name'  => 'ワークフロー作業',
			'explanation'  => 'ワークフロー管理下コントローラにおける新規作成、申請、編集権限を行います。',
			'acl_exp'      => 'ワークフロー管理下コントローラにおける新規作成、申請、編集権限です。不可視項目の閲覧権限などに依存します。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'::action_view',
				$controller.'::action_edit',
				$controller.'::action_create',
				$controller.'::action_index',
				$controller.'::action_index_admin',
				$controller.'::action_index_invisible',
				$controller.'::action_view_invisible',
				$controller.'::action_index_workflow',
				$controller.'::action_apply',
				$controller.'::action_route',
			)
		);
		return $retvals;
	}

	/**
	 * workflow_process()
	 */
	public static function actionset_route($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'url'          => '',
			'action_name'  => 'ワークフロー承認',
			'explanation'  => 'ワークフロー管理下コントローラにおける承認権限です。',
			'acl_exp'      => 'ワークフロー管理下コントローラにおける承認権限です。承認設定は、ワークフローコントローラの経路設定で別途設定します。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'::action_index',
				$controller.'::action_index_admin',
				$controller.'::action_view',
				$controller.'::action_index_invisible',
				$controller.'::action_view_invisible',
				$controller.'::action_index_workflow',
				$controller.'::action_approve',
				$controller.'::action_reject',
				$controller.'::action_remand',
			)
		);
		return $retvals;
	}

	/**
	 * workflow_actions()
	 * 重たい処理。ワークフローが不要なコントローラでは読まないように注意。
	 */
	public static function actionset_approve($controller, $obj = null, $id = null, $urls = array())
	{
		$retval = array('dependencies'=>array());
		if (is_null($controller) || empty($obj) || ! isset($obj->id)) return $retval;

		//ステップなどを取得
		$model_name = str_replace('Controller_', 'Model_', $controller);
		$model = $model_name::forge();

		$current_step    = $model::get_current_step($controller, $obj->id);
		$route_id        = $model::get_route($controller, $obj->id);
		$current_step_id = $model::get_current_step_id($route_id, $current_step);
		$total_step      = $route_id ? $model::get_total_step($route_id) : -2;
		$user_id         = \Auth::get('id');
		$urls            = array();

		//-1の場合は、承認申請
		if ($current_step == -1)
		{
			if (\Auth::has_access($controller.'::action_apply'))
			{
				$urls = array(
					\Html::anchor(\Inflector::ctrl_to_dir("{$controller}/apply/{$obj->id}"), '承認申請'),
					\Html::anchor(\Inflector::ctrl_to_dir("{$controller}/route/{$obj->id}"), '経路再設定')
				);
			}
		}
		elseif ($current_step < $total_step)
		{
		//ワークフロー進行中だったら承認・却下・差戻しができる
			$members = $model::get_members($current_step_id);
			if ($obj->workflow_status == 'in_progress' && in_array($user_id, $members))
			{
				$urls = array(
					\Html::anchor(\Inflector::ctrl_to_dir("{$controller}/approve/{$obj->id}"), '承認'),
					\Html::anchor(\Inflector::ctrl_to_dir("{$controller}/remand/{$obj->id}"), '差戻し'),
					\Html::anchor(\Inflector::ctrl_to_dir("{$controller}/reject/{$obj->id}"), '却下'),
				);
			}
			$menu_str = '';
		}
		elseif ($current_step == $total_step)
		{
		//すでに承認が終わっていたらワークフローとしては、何もできない
			$urls = array() ;
		}

		// ワークフロー進行中は編集と削除はできない
		if ($obj->workflow_status == 'in_progress')
		{
			\Actionset::disabled(array('base' => array('edit','delete')));
		}

		//経路が設定されていなければ、申請できない。経路設定URLを表示
		if (
			$model::get_current_step($controller, $obj->id) == -2 &&
			\Auth::has_access($controller.'::action_route') &&
			$obj->workflow_status !== 'finish'
		)
		{
			$urls = array(\Html::anchor(\Inflector::ctrl_to_dir("{$controller}/route/{$obj->id}"), '経路設定')) ;
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ワークフロー作業（承認申請）',
			'explanation'  => 'ワークフロー管理下コントローラにおける承認申請です。',
			'acl_exp'      => 'ワークフロー管理下コントローラにおける承認申請です。「ワークフロー作業」を有効にすると自動的に有効になります。',
			'order'        => 100,
			'dependencies' => array()
		);
		return $retvals;
	}
}
