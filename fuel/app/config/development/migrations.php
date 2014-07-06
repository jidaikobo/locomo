<?php
return array(
	'version' => 
	array(
		'app' => 
		array(
			'default' => 
			array(
				0 => '001_create_users',
				1 => '002_create_usergroups',
				2 => '003_create_users_usergroups_r',
				3 => '004_create_acls',
				4 => '005_create_meta',
				5 => '006_create_loginlog',
			),
		),
		'module' => 
		array(
		),
		'package' => 
		array(
			'kontiki' => 
			array(
				0 => '002_create_usergroups',
				1 => '003_create_users_usergroups_r',
				2 => '004_create_acls',
				3 => '005_create_loginlog',
			),
		),
	),
	'folder' => 'migrations/',
	'table' => 'migration',
);
