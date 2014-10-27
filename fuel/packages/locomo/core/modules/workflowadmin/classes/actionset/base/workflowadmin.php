<?php
namespace Workflowadmin;
class Actionset_Base_Workflowadmin extends \Actionset
{
	/**
	 * index_admin()
	 * @return  array
	 */
	public static function actionset_index_admin($module, $obj, $get_authed_url)
	{
		$retvals = array(
			'url'          => 'workflowadmin/index_admin',
			'action_name'  => 'ワークフロー管理',
			'menu_str'     => 'ワークフロー管理',
			'explanation'  => 'ワークフロー管理。管理者のみアクセス可能。',
			'dependencies' => array(
				'index_admin',
			)
		);
		return $retvals;
	}

	/**
	 * view()
	 * @return  array
	 */
	public static function actionset_view($module, $obj, $get_authed_url)
	{
		$url = isset($item->id) ? "$controller/view/$item->id" : null ;

		$retvals = array(
			'url'           => $url,
			'action_name'   => 'ワークフロー閲覧',
			'menu_str'      => 'ワークフロー閲覧',
			'explanation'   => 'ワークフローの閲覧権限です。',
			'dependencies'  => array(
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 * @return  array
	 */
	public static function actionset_edit($module, $obj, $get_authed_url)
	{
		$url = isset($item->id) ? "$controller/edit/$item->id" : null ;

		$retvals = array(
			'url'           => $url,
			'action_name'   => 'ワークフロー名称の編集',
			'menu_str'      => 'ワークフロー名称の編集',
			'explanation'   => 'ワークフロー名称の編集権限',
			'dependencies'  => array(
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
	public static function actionset_create($module, $obj, $get_authed_url)
	{
		$retvals = array(
			'is_admin_only' => true,
			'url'           => 'workflowadmin/create',
			'action_name'   => '新規ワークフロー',
			'menu_str'      => '新規ワークフロー',
			'explanation'   => '新規ワークフロー作成フォーム。管理者のみアクセス可能。',
			'dependencies'  => array(
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
	public static function actionset_setup($module, $obj, $get_authed_url)
	{
		$url = isset($item->id) ? "workflowadmin/setup/{$item->id}" : null ;
		$retvals = array(
			'is_admin_only' => true,
			'url'           => $url,
			'action_name'   => 'ワークフローの設定',
			'menu_str'      => 'ワークフローの設定',
			'explanation'   => 'ワークフローの設定。管理者のみアクセス可能。',
			'dependencies'  => array(
				'setup',
			)
		);

		return $retvals;
	}
}
