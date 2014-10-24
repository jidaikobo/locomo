<?php
namespace Scaffold;
class Actionset_Base_Scaffold extends \Actionset
{
	/**
	 * actionset_main()
	 * @return  array
	 */
	public static function actionset_main($module, $obj, $get_authed_url)
	{
		$retvals = array(
			'is_admin_only' => true,
			'url'           => '/scaffold/main',
			'menu_str'      => '足場組みのフォーム',
		);
		return $retvals;
	}
}
