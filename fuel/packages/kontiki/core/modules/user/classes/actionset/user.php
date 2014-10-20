<?php
namespace Kontiki_Core_Module\User;
class Actionset_User extends \Kontiki\Actionset
{
	/**
	 * actionItems()
	 * @return  obj
	 */
	public static function actionItems($controller = null, $item = null)
	{
		$actions = parent::actionItems($controller, $item);
//		$actions->usergroups = self::usergroups($controller, $item);
		return $actions;
	}

	/**
	 * usergroups()
	 * @return  array
	 */
/*
	private static function usergroups($controller, $item)
	{
		$url = parent::check_auth($controller, 'usergroups') ? "{$controller}/option/usergroups" : '';
		$url_rev = $url ? "{$controller}/option_revisions/usergroups" : '';
		$urls = array(
			array('ユーザグループ設定', $url),
			array('ユーザグループ設定履歴', $url_rev),
		);

		$retvals = array(
			'is_admin_only' => false,
			'is_index'      => true,
			'url'           => $urls,
			'id_segment'    => null,
			'action_name'   => 'ユーザグループ',
			'explanation'   => 'ユーザが所属するユーザグループです。コントローラオプションのため、この権限を許可するとほかのオプション類も許可されます。',
			'menu_str'      => '',
			'dependencies' => array(
				'option',
				'option_revisions',
//				'option/usergroups',
//				'option_revisions/usergroups',
			)
		);
		return $retvals;
	}
*/
}
