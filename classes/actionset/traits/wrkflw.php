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
		// count を数えると未承認の項目全てを見るので count は返さない
		/*
		static $count;
		$model = str_replace('Controller', 'Model', $controller);
		if (class_exists($model) && ! $count)
		{
			$pk = $model::primary_key()[0];
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
		$urls = array(array($controller.DS."index_workflow", "承認項目一覧{$count}"));
		 */

		$urls = array(array($controller.DS."index_workflow", "承認項目一覧"));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls,
			'action_name'  => 'ワークフロー承認項目一覧',
			'explanation'  => '現在承認すべき項目の一覧です。',
			'order'        => 100,
			'show_at_top'  => true,
			'dependencies' => array(
				$controller.'/index_workflow',
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
			'order'        => 10,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/edit',
				$controller.'/create',
				$controller.'/index',
				$controller.'/index_admin',
				$controller.'/index_invisible',
				$controller.'/view_invisible',
				$controller.'/index_workflow',
				$controller.'/apply',
				$controller.'/route',
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
			'order'        => 10,
			'dependencies' => array(
				$controller.'/index',
				$controller.'/index_admin',
				$controller.'/view',
				$controller.'/index_invisible',
				$controller.'/view_invisible',
				$controller.'/index_workflow',
				$controller.'/approve',
				$controller.'/reject',
				$controller.'/remand',
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
			if (\Auth::has_access($controller.'/apply'))
			{
				$urls = array(
					array("{$controller}/apply/{$obj->id}", '承認申請'),
					array("{$controller}/route/{$obj->id}", '経路再設定')
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
					array("{$controller}/approve/{$obj->id}", '承認'),
					array("{$controller}/remand/{$obj->id}", '差戻し'),
					array("{$controller}/reject/{$obj->id}", '却下'),
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
			\Actionset::disabled(array(
				$controller.'/edit',
				$controller.'/delete'
			));
		}

		//経路が設定されていなければ、申請できない。経路設定URLを表示
		if (
			$model::get_current_step($controller, $obj->id) == -2 &&
			\Auth::has_access($controller.'/route') &&
			$obj->workflow_status !== 'finish'
		)
		{
			$urls = array(array("{$controller}/route/{$obj->id}", '経路設定')) ;
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ワークフロー作業（承認申請）',
			'explanation'  => 'ワークフロー管理下コントローラにおける承認申請です。',
			'order'        => 100,
			'dependencies' => array()
		);
		return $retvals;
	}
}
