<?php
return array(
	'nicename'        => 'ヘルプ',
	'index_nicename'  => 'ヘルプ',
	'adminindex'      => 'help/help/index_admin',
	'is_admin_only'   => true,
	'order_in_menu'   => 100,//help module won't appeared at controller menu by inc_admin_bar.html
	'main_controller' => '\\Help\\Controller_Help',
	'actionset_classes' => array(
		'base'   => '\\Help\\Actionset_Base_Help',
		'index'  => '\\Help\\Actionset_Index_Help',
		'option' => '\\Help\\Actionset_Option_Help',
	),
);
