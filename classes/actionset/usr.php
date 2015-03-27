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
			$urls = array(array($controller.DS."reset_paswd/".$id, 'パスワードリセット'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'order'        => 100,
		);

		return $retvals;
	}

	/**
	 * actionset_bulk_reset_paswd()
	 */
	public static function actionset_bulk_reset_paswd($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Auth::is_root())
		{
			$urls = array(array($controller.DS."bulk_reset_paswd", '一括パスワードリセット'));
		}

		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'order'        => 100,
		);

		return $retvals;
	}
}
