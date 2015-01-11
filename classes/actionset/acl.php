<?php
namespace Locomo;
class Actionset_Acl extends \Actionset
{
	/**
	 * actionset_controller_index()
	 */
	public static function actionset_controller_index($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'アクセス権管理',
			'show_at_top'  => true,
			'order'        => 1,
		);
		return $retvals;
	}

	/**
	 * actionset_actionset_index()
	 */
	public static function actionset_actionset_index($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => $urls ,
			'show_at_top'  => false,
			'action_name'  => 'アクセス権管理',
			'help'         => '
# 依存関係について
依存した行為を許可すると、自動的にほかの行為が許可される場合があります。たとえば「項目を編集する権利」を持った人は、「通常項目を閲覧する権利」が自動的に許可されます。

# ログインユーザ権限
「ログインユーザすべて」に行為を許可している場合、個別にアクセス権を与えなくても、許可された状態になっていることがあります。
',
			'order'        => 1,
		);
		return $retvals;
	}
}
