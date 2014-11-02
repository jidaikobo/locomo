<?php
namespace Workflow;
trait Traits_Actionset_Base_Workflow
{
	/**
	 * actionset_index_workflow()
	 */
	public static function actionset_index_workflow($module, $obj, $id, $urls = array())
	{
		$url_str = $module."/index_workflow" ;
		$urls = \Auth::auth($url_str) ? $url_str : '' ;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ワークフロー承認項目一覧',
			'explanation'  => '現在承認すべき項目の一覧です。「ワークフロー作業」「ワークフロー承認」権限と同時に自動的に付与されます。',
			'order'        => 10,
			'dependencies' => array(
				'index_workflow',
			)
		);
		return $retvals;
	}

	/**
	 * workflow()
	 */
	public static function actionset_workflow($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name'  => 'ワークフロー作業',
			'explanation'  => 'ワークフロー管理下コントローラにおける新規作成、申請、編集権限です。不可視項目の閲覧権限などに依存します。',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'edit',
				'create',
				'index',
				'index_admin',
				'index_invisible',
				'view_invisible',
				'index_workflow',
				'apply',
				'route',
			)
		);
		return $retvals;
	}

	/**
	 * workflow_process()
	 */
	public static function actionset_workflow_process($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'url'          => '',
			'action_name'  => 'ワークフロー承認',
			'explanation'  => 'ワークフロー管理下コントローラにおける承認権限です。承認設定は、ワークフローコントローラの経路設定で別途設定します。',
			'order'        => 10,
			'dependencies' => array(
				'index',
				'index_admin',
				'view',
				'index_invisible',
				'view_invisible',
				'index_workflow',
				'approve',
				'reject',
				'remand',
			)
		);
		return $retvals;
	}

	/**
	 * workflow_actions()
	 * 重たい処理。ワークフローが不要なコントローラでは読まないように注意。
	 */
	public static function actionset_workflow_actions($module, $obj, $id, $urls = array())
	{
		$retval = array('dependencies'=>array());
		if(is_null($module) || empty($obj) || ! isset($obj->id)) return $retval;

		//ステップなどを取得
		$model_name = '\\'.ucfirst($module).'\\Model_'.ucfirst($module);
		$model = $model_name::forge();

		$current_step    = $model::get_current_step($module, $obj->id);
		$route_id        = $model::get_route($module, $obj->id);
		$current_step_id = $model::get_current_step_id($route_id, $current_step);
		$total_step      = $route_id ? $model::get_total_step($route_id) : -2;
		$user_id         = \Auth::get_user_id();
		$url             = '';
		$menu_str        = '';

		//-1の場合は、承認申請
		if($current_step == -1):
			if(\Auth::auth($module.'/apply')):
				$url = "{$module}/apply/{$obj->id}" ;
				$menu_str = '承認申請';
			endif;
		elseif($current_step < $total_step):
		//ワークフロー進行中だったら承認・却下・差戻しができる
			$members = $model::get_members($route_id, $current_step_id);
			if(is_array($members) && in_array($user_id, $members)):
				$url = array(
					array('承認',   "{$module}/approve/{$obj->id}"),
					array('却下',   "{$module}/reject/{$obj->id}"),
					array('差戻し', "{$module}/remand/{$obj->id}"),
				);
			endif;
			$menu_str = '';
		elseif($current_step == $total_step):
		//すでに承認が終わっていたらワークフローとしては、何もできない
			$url = "" ;
			$menu_str = '';
		endif;

		//経路が設定されていなければ、申請できない。経路設定URLを表示
		if(
			$model::get_current_step($module, $obj->id) == -2 &&
			\Auth::auth($module.'/route')
		):
			$url = "{$module}/route/{$obj->id}" ;
			$menu_str = '経路設定';
		endif;

		$retvals = array(
			'url'          => $url,
			'action_name'  => 'ワークフロー作業（承認申請）',
			'menu_str'     => $menu_str,
			'explanation'  => 'ワークフロー管理下コントローラにおける承認申請です。「ワークフロー作業」を有効にすると自動的に有効になります。',
			'order'        => 10,
			'dependencies' => array()
		);
		return $retvals;
	}
}
