<?php
namespace User;
class Actionset_Option_User extends \Actionset_Option
{
	use \Locomo\Actionset_Traits_Testdata;

	/**
	 * actionset_usergroup()
	 */
	public static function actionset_usergroup($module, $obj, $id, $urls = array())
	{
		//urls
		$actions = array(array("/user/usergroup/", 'ユーザグループ'));
		$urls = static::generate_anchors('user', 'usergroup', $actions, $obj);

		//overrides
		$overrides = static::generate_bulk_anchors(
			$module      = 'user',
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
				'usergroup',
			)
		);
		return $retvals;
	}
}
