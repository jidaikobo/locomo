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
<<<<<<< HEAD
			'revision' => 
			array(
				0 => '001_create_revision',
=======
			'post2' => 
			array(
				0 => '001_create_post2',
>>>>>>> 7f1c89855fcb2e0585e2257950bf3cbff116af40
			),
		),
		'package' => 
		array(
		),
	),
	'folder' => 'migrations/',
	'table' => 'migration',
);
