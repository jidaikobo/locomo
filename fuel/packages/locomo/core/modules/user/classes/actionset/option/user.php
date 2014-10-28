<?php
namespace User;
class Actionset_Option_User extends \Actionset_Option
{
	/**
	 * actionset_usergroup()
	 */
	public static function actionset_usergroup($module, $obj, $get_authed_url)
	{
		if($get_authed_url):
			$url = self::check_auth($module, 'usergroup_bulk') ? "user/usergroup_bulk" : '';
			$url_new = $url ? "user/usergroup_bulk/?create=1" : '';
			$url_rev = $url ? "user/option_revisions/usergroup" : '';
			$urls = array(
				array('ユーザグループ設定', $url),
				array('ユーザグループ新規作成', $url_new),
				array('ユーザグループ設定履歴', $url_rev),
			);
		endif;

		$retvals = array(
			'is_admin_only' => false,
			'url'           => @$urls ?: array(),
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
