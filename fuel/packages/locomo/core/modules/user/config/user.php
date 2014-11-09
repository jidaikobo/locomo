<?php

return array(
	'nicename'          => 'ユーザ',
	'index_nicename'    => 'ユーザ管理',
	'adminindex'        => 'user/user/index_admin',
	'is_admin_only'     => true,
	'order_in_menu'     => 10,
	'main_controller'   => '\\User\\Controller_User',
	'actionset_classes' => array(
		'base'   => '\\User\\Actionset_Base_User',
		'index'  => '\\User\\Actionset_Index_User',
		'option' => '\\User\\Actionset_Option_User',
	),
);
