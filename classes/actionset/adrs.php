<?php
class Actionset_Adrs extends \Actionset_Base
{
//	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;
//	use \Actionset_Traits_Testdata;

	/**
	 * actionset_edit_adrsgrp()
	 */
	public static function actionset_edit_adrsgrp($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(
			array($controller.DS."edit_adrsgrp/", 'グループの設定'),
			array($controller.DS."edit_adrsgrp/?create=1", 'グループの新規作成'),
		);
		$urls = static::generate_urls($controller.'::action_edit_adrsgrp', $actions, ['create']);

		$retvals = array(
			'realm'        => 'option' ,
			'urls'         => $urls ,
			'action_name'  => 'グループの設定',
			'show_at_top'  => true,
			'explanation'  => 'アドレス帳のグループ設定です。',
			'acl_exp'      => 'アドレス帳のグループ設定権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'::action_edit_adrsgrp',
			)
		);
		return $retvals;
	}
}
