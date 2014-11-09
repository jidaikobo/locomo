<?php
return array(
	'nicename'        => 'ワークフローテスト',      //モジュール名称
	'index_nicename'  => 'ワークフローテスト',      //モジュール名称
	'adminindex'      => 'wftest/wftest/index_admin', //モジュールの管理者向けインデクス
	'is_admin_only'   => false,                 //trueだと、aclの候補にならず、かつ管理者向けメニューにか表示されなくなります
	'order_in_menu'   => 100,                   //ログイン後のメニューの表示順。小さいほど上
	'main_controller' => '\\Wftest\\Controller_Wftest',
	'actionset_classes' => array(
		'base'   => '\\Wftest\\Actionset_Base_Wftest',
		'index'  => '\\Wftest\\Actionset_Index_Wftest',
		'option' => '\\Wftest\\Actionset_Option_Wftest',
	),
);
