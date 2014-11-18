<?php
namespace Workflow;
class Actionset_Base_Workflow extends \Actionset
{
	/**
	 * view()
	 * @return  array
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'edit' && $id):
			$actions = array(array($controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($controller, 'view', $actions, ['create']);
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
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'view' && $id):
			$actions = array(array($controller.DS."edit/".$id, '編集'));
			$urls = static::generate_uris($controller, 'edit', $actions, ['edit','create']);
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
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."create", '新規作成'));
		$urls = static::generate_uris($controller, 'create', $actions, ['create']);

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '新規ワークフロー',
			'show_at_top'  => true,
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
	public static function actionset_setup($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'view' && $id):
			$actions = array(array($controller.DS."setup/".$id, '設定'));
			$urls = static::generate_uris($controller, 'setup', $actions, ['setup','create']);
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
