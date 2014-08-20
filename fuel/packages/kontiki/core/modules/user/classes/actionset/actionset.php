<?php
namespace User;
class Actionset_User extends \Kontiki\Actionset
{
	/**
	 * actionItems()
	 * @return  obj
	 */
	public static function actionItems($controller = null, $item = null)
	{
		$actions = parent::actionItems($controller, $item);
		$actions->usergroups = self::usergroups($controller, $item);
		return $actions;
	}

	/**
	 * usergroups()
	 * @return  array
	 */
	private static function usergroups($controller, $item)
	{
		$url = parent::check_auth($controller, 'usergroups') ? "{$controller}/options/usergroups" : '';
		$url_rev = $url ? "{$controller}/options_revisions/usergroups" : '';
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
			'explanation'   => 'ユーザが所属するユーザグループです。',
			'menu_str'      => '',
			'dependencies' => array(
				'options/usergroups',
				'options_revisions/usergroups',
			)
		);
		return $retvals;
	}
}
