<?php
namespace Workflow;
class Actionset
{
	/**
	 * actionItems()
	 * @return  obj
	 */
	public static function actionItems($controller = null, $item = null)
	{
		$actions = (object) array();
		$actions->index_admin = self::index_admin($controller, $item);
		$actions->view        = self::view($controller, $item);
		$actions->edit        = self::edit($controller, $item);
		$actions->create      = self::create($controller, $item);
		$actions->setup       = self::setup($controller, $item);
		return $actions;
	}

	/**
	 * index_admin()
	 * @return  array
	 */
	private static function index_admin($controller, $item)
	{
		$retvals = array(
			'is_index'     => true,
			'url'          => 'workflow/index_admin',
			'id_segment'   => null,
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
	private static function view($controller, $item)
	{
		$url = isset($item->id) ? "$controller/view/$item->id" : null ;

		$retvals = array(
			'url'           => $url,
			'id_segment'    => 3,
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
	private static function edit($controller, $item)
	{
		$url = isset($item->id) ? "$controller/edit/$item->id" : null ;

		$retvals = array(
			'url'           => $url,
			'id_segment'    => 3,
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
	private static function create($controller, $item)
	{
		$retvals = array(
			'url'           => 'workflow/create',
			'id_segment'    => null,
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
	private static function setup($controller, $item)
	{
		$url = isset($item->id) ? "$controller/setup/$item->id" : null ;
		$retvals = array(
			'url'           => $url,
			'id_segment'    => 3,
			'action_name'   => 'ワークフローの設定',
			'menu_str'      => 'ワークフローの設定',
			'explanation'   => 'ワークフローの設定。管理者のみアクセス可能。',
			'dependencies'  => array(
				'view',
				'create',
			)
		);

		return $retvals;
	}
}
