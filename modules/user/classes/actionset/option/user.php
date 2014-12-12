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
		$actions = array(array("/user/usergroup", 'ユーザグループ'));
		$urls = static::generate_urls($controller, 'create', $actions);

		// retvals
		$retvals = array(
			'urls'         => $urls,
			'order'        => 10,
		);
		return $retvals;
	}
}
