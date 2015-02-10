<?php
namespace Locomo;
class Actionset_Usr extends \Actionset_Base
{
	// traits
	use \Actionset_Traits_Revision;
	use \Actionset_Traits_Testdata;

	/**
	 * actionset_reset_paswd()
	 */
	public static function actionset_reset_paswd($controller, $obj = null, $id = null, $urls = array())
	{
		if (in_array(\Request::main()->action, ['edit', 'view']) && $id)
		{
			$actions = array(array($controller.DS."reset_paswd/".$id, 'パスワードリセット'));
			$urls = static::generate_urls($controller.'::action_edit', $actions);
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'order'        => 100,
		);

		return $retvals;
	}
}
