<?php
return array(
	'nicename'        => 'テストモジュール',         //モジュール名称
	'index_nicename'  => 'テストモジュール',         //モジュール名称
	'adminindex'      => 'test1/test1/index_admin', //モジュールの管理者向けインデクス
	'is_admin_only'   => false,                     //trueだと、aclの候補にならず、かつ管理者向けメニューにか表示されなくなります
	'order_in_menu'   => 100,
	'main_controller' => '\\Test1\\Controller_Test1',



	'controllers' => array(
		'\\Test1\\Controller_Test1' =>array(
			'nicename' => 'テスト1',
			'actionset_classes' =>array(
				'base'   => '\\Test1\\Actionset_Base_Test1',
				'index'  => '\\Test1\\Actionset_Index_Test1',
				'option' => '\\Test1\\Actionset_Option_Test1',
			),
		),
		'\\Test1\\Controller_Test2' =>array(
			'nicename' => 'テスト2',
			'actionset_classes' =>array(
				'base'   => '\\Test1\\Actionset_Base_Test2',
			),
		),
	),



	'actionset_classes' => array(
		'\\Test1\\Controller_Test1' =>array(
			'base'   => '\\Test1\\Actionset_Base_Test1',
			'index'  => '\\Test1\\Actionset_Index_Test1',
			'option' => '\\Test1\\Actionset_Option_Test1',
		),
		'\\Test1\\Controller_Test2' =>array(
			'base'   => '\\Test1\\Actionset_Base_Test2',
		),
	),
);
