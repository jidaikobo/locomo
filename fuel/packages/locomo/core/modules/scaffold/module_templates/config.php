<?php
return array(
	'nicename'        => '###nicename###',      //モジュール名称
	'index_nicename'  => '###nicename###',      //モジュール名称
	'adminindex'      => 'xxx/xxx/index_admin', //モジュールの管理者向けインデクス
	'is_admin_only'   => false,                 //trueだと、aclの候補にならず、かつ管理者向けメニューにか表示されなくなります
	'order_in_menu'   => 100,                   //ログイン後のメニューの表示順。小さいほど上
	'main_controller' => '\\XXX\\Controller_XXX',
	'actionset_classes' => array(
		'base'   => '\\XXX\\Actionset_Base_XXX',
		'index'  => '\\XXX\\Actionset_Index_XXX',
		'option' => '\\XXX\\Actionset_Option_XXX',
	),
);
