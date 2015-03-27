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
		if (\Request::main()->action != 'create')
		{
			$urls = array(
				array($controller.DS."edit_adrsgrp/", 'グループの設定'),
				array($controller.DS."edit_adrsgrp/?create=1", 'グループの新規作成'),
			);
		}

		$retvals = array(
			'realm'        => 'option' ,
			'urls'         => $urls ,
			'action_name'  => 'グループの設定',
			'show_at_top'  => true,
			'explanation'  => 'アドレス帳のグループ設定です。',
			'acl_exp'      => 'アドレス帳のグループ設定権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'/edit_adrsgrp',
			)
		);
		return $retvals;
	}
}
