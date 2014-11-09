<?php
return array(
	'nicename'        => 'ヘルプ',      //モジュール名称
	'index_nicename'  => 'ヘルプ',      //モジュール名称
	'adminindex'      => 'help/help/index_admin', //モジュールの管理者向けインデクス
	'is_admin_only'   => false,                 //trueだと、aclの候補にならず、かつ管理者向けメニューにか表示されなくなります
	'order_in_menu'   => 100,                   //ログイン後のメニューの表示順。小さいほど上
	'main_controller' => '\\Help\\Controller_Help',
	'actionset_classes' => array(
		'base'   => '\\Help\\Actionset_Base_Help',
		'index'  => '\\Help\\Actionset_Index_Help',
		'option' => '\\Help\\Actionset_Option_Help',
	),
);
