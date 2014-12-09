<?php
namespace Workflow;
trait Traits_Actionset_Base_Workflow
{
	/**
	 * actionset_index_workflow()
	 */
	public static function actionset_index_workflow($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index_workflow", '承認項目一覧'));
		$urls = static::generate_uris($controller, 'index_workflow', $actions, ['index_workflow']);

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ワークフロー承認項目一覧',
			'explanation'  => '現在承認すべき項目の一覧です。「ワークフロー作業」「ワークフロー承認」権限と同時に自動的に付与されます。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'index_workflow',
			)
		);
		return $retvals;
	}

	/**
	 * workflow()
	 */
	public static function actionset_workflow($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'action_name'  => 'ワークフロー作業',
			'explanation'  => 'ワークフロー管理下コントローラにおける新規作成、申請、編集権限です。不可視項目の閲覧権限などに依存します。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'view',
				$controller.DS.'edit',
				$controller.DS.'create',
				$controller.DS.'index',
				$controller.DS.'index_admin',
				$controller.DS.'index_invisible',
				$controller.DS.'view_invisible',
				$controller.DS.'index_workflow',
				$controller.DS.'apply',
				$controller.DS.'route',
			)
		);
		return $retvals;
	}

	/**
	 * workflow_process()
	 */
	public static function actionset_workflow_process($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'url'          => '',
			'action_name'  => 'ワークフロー承認',
			'explanation'  => 'ワークフロー管理下コントローラにおける承認権限です。承認設定は、ワークフローコントローラの経路設定で別途設定します。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'index',
				$controller.DS.'index_admin',
				$controller.DS.'view',
				$controller.DS.'index_invisible',
				$controller.DS.'view_invisible',
				$controller.DS.'index_workflow',
				$controller.DS.'approve',
				$controller.DS.'reject',
				$controller.DS.'remand',
			)
		);
		return $retvals;
	}

	/**
	 * workflow_actions()
	 * 重たい処理。ワークフローが不要なコントローラでは読まないように注意。
	 */
	public static function actionset_workflow_actions($controller, $obj = null, $id = null, $urls = array())
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
			if (\Auth::instance()->has_access($controller.'/apply'))
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
			if (is_array($members) && in_array($user_id, $members))
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

		//経路が設定されていなければ、申請できない。経路設定URLを表示
		if (
			$model::get_current_step($controller, $obj->id) == -2 &&
			\Auth::instance()->has_access($controller.'/route')
		)
		{
			$urls = array(\Html::anchor(\Inflector::ctrl_to_dir("{$controller}/route/{$obj->id}"), '経路設定')) ;
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ワークフロー作業（承認申請）',
			'explanation'  => 'ワークフロー管理下コントローラにおける承認申請です。「ワークフロー作業」を有効にすると自動的に有効になります。',
			'order'        => 100,
			'dependencies' => array()
		);
		return $retvals;
	}
}
