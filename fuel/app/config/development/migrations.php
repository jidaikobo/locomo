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
		),
		'package' => 
		array(
		),
	),
	'folder' => 'migrations/',
	'table' => 'migration',
);
