<?php
return array(
	'nicename'        => 'サンプルモジュール',
	'index_nicename'  => 'サンプルモジュール',
	'adminindex'      => 'index_admin',
	'is_admin_only'   => false,
	'order_in_menu'   => 100,
	'main_controller' => '\\Sample\\Controller_Sample',
	'actionset_classes' => array(
		'base'   => '\\Sample\\Actionset_Base_Sample',
		'index'  => '\\Sample\\Actionset_Index_Sample',
		'option' => '\\Sample\\Actionset_Option_Sample',
	),
);
