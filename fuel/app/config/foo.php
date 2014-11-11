<?php

return array(
	'nicename'        => 'FOO',
	'index_nicename'  => 'FOOコントローラ',
	'adminindex'      => 'foo/index',
	'is_admin_only'   => false,
	'order_in_menu'   => 30,
	'adminmodule'     => true,
	'main_controller' => '\\Controller_Foo',
	'actionset_classes' => array(
		'base'   => '\\Actionset_Foo',
	),
);
