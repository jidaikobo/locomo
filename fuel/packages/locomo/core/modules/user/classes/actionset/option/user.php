<?php
namespace User;
class Actionset_Option_User extends \Actionset_Option
{
	use \Locomo\Actionset_Traits_Testdata;

	/**
	 * actionset_usergroup()
	 */
	public static function actionset_usergroup($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."usergroup", 'ユーザグループ'));
		$urls = static::generate_uris($module, $controller, 'create', $actions);

		//overrides
		$overrides = static::generate_bulk_anchors(
			$module      = 'user',
			$controller  = 'user',
			$model       = 'usergroup',
			$opt         = 'usergroup',
			$nicename    = 'ユーザグループ',
			$is_override = $urls
		);

		$retvals = array(
			'urls'         => $urls,
			'overrides'    => $overrides,
			'action_name'  => 'ユーザグループ',
			'explanation'  => 'ユーザが所属するユーザグループの設定権限です。',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'usergroup',
			)
		);
		return $retvals;
	}
}
