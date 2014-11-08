<?php
class Actionset_Foo extends \Locomo\Actionset
{
	/**
	 * create()
	 */
	public static function actionset_create($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."create", '新規作成'));
		$urls = static::generate_uris($module, $controller, 'create', $actions, ['create']);

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '新規作成',
			'explanation'  => '新規作成権限',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'index',
				$controller.DS.'view',
				$controller.DS.'create',
			)
		);

		return $retvals;
	}

	/**
	 * view()
	 */
	public static function actionset_view($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'edit' && $id):
			$actions = array(array($module.DS.$controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($module, $controller, 'view', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '閲覧（通常項目）',
			'explanation'  => '通常項目の個票の閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'index',
				$controller.DS.'view',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'view' && $id):
			$actions = array(array($module.DS.$controller.DS."edit/".$id, '編集'));
			$urls = static::generate_uris($module, $controller, 'edit', $actions, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '編集（通常項目）',
			'explanation'  => '通常項目の編集権限',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'view',
				$controller.DS.'edit',
			)
		);
		return $retvals;
	}

}
