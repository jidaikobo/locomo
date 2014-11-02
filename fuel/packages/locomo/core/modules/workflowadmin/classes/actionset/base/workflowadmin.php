<?php
namespace Workflowadmin;
class Actionset_Base_Workflowadmin extends \Actionset
{
	/**
	 * index_admin()
	 * @return  array
	 */
	public static function actionset_index_admin($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name'  => 'ワークフロー管理',
			'explanation'  => 'ワークフロー管理。管理者のみアクセス可能。',
			'order'        => 10,
			'dependencies' => array()
		);
		return $retvals;
	}

	/**
	 * view()
	 * @return  array
	 */
	public static function actionset_view($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array("workflowadmin/view/".$id, '閲覧'));
			$urls = static::generate_anchors('workflowadmin', 'view', $actions, $obj, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフロー閲覧',
			'explanation'  => 'ワークフローの閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 * @return  array
	 */
	public static function actionset_edit($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array("workflowadmin/edit/".$id, '閲覧'));
			$urls = static::generate_anchors('workflowadmin', 'view', $actions, $obj, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフロー名称の編集',
			'explanation'  => 'ワークフロー名称の編集権限',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'edit',
			)
		);
		return $retvals;
	}
	
	/**
	 * create()
	 * @return  array
	 */
	public static function actionset_create($module, $obj, $id, $urls = array())
	{
		$actions = array(array("workflowadmin/create", '新規作成'));
		$urls = static::generate_anchors('workflowadmin', 'create', $actions, $obj, ['create']);

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '新規ワークフロー',
			'explanation'  => '新規ワークフロー作成フォーム。管理者のみアクセス可能。',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'create',
			)
		);

		return $retvals;
	}

	/**
	 * setup()
	 * @return  array
	 */
	public static function actionset_setup($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array("workflowadmin/setup/".$id, '閲覧'));
			$urls = static::generate_anchors('workflowadmin', 'setup', $actions, $obj);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフローの設定',
			'explanation'  => 'ワークフローの設定。管理者のみアクセス可能。',
			'order'        => 10,
			'dependencies' => array()
		);

		return $retvals;
	}
}
