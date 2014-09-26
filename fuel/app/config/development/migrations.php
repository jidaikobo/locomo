<?php
return array(
	'version' => 
	array(
		'app' => 
		array(
			'default' => 0,
		),
		'module' => 
		array(
			'workflowadmin' => 
			array(
				0 => '001_create_workflowadmin',
			),
			'post' => 
			array(
				0 => '001_create_post',
				1 => '002_create_postcategories',
			),
			'user' => 
			array(
				0 => '001_create_users',
				1 => '002_create_usergroups',
			),
			'revision' => 
			array(
				0 => '001_create_revision',
			),
		),
		'package' => 
		array(
		),
	),
	'folder' => 'migrations/',
	'table' => 'migration',
);
