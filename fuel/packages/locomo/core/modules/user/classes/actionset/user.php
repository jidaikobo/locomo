<?php
namespace Locomo_Core_Module\User;
class Actionset_User extends \Actionset
{
	/**
	 * set_actionset()
	 * @return  obj
	 */
	public static function set_actionset($module = null, $obj = null)
	{
		parent::set_actionset($module, $obj);
		static::$actions->usergroup = self::usergroup($module, $obj);
	}

	/**
	 * usergroup()
	 * @return  array
	 */
	private static function usergroup($module, $obj)
	{
		$url = parent::check_auth($module, 'usergroup') ? "{$module}/option/usergroup" : '';
		$url_rev = $url ? "{$module}/option_revisions/usergroup" : '';
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
//				'option/usergroup',
//				'option_revisions/usergroup',
			)
		);
		return $retvals;
	}
}
