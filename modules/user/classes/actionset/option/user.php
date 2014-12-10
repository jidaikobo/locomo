<?php
namespace User;
class Actionset_Option_User extends \Actionset_Option
{
	use \Locomo\Actionset_Traits_Option_Testdata;

	/**
	 * actionset_usergroup()
	 */
	public static function actionset_usergroup($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."usergroup", 'ユーザグループ'));
		$urls = static::generate_uris($controller, 'create', $actions);

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
			'show_at_top'  => true,
			'action_name'  => 'ユーザグループ',
			'explanation'  => 'ユーザグループ管理です。追加、削除、設定等を行います。',
			'acl_exp'      => 'ユーザグループ管理です。追加、削除、設定等の権限です。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'usergroup',
			)
		);
		return $retvals;
	}
}
