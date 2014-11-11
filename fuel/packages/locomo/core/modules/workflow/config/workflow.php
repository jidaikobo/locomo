<?php
return array(
	'nicename'        => 'ワークフロー',
	'index_nicename'  => '',
	'adminindex'      => '',
	'is_admin_only'   => true,
	'order_in_menu'   => 90,
//	'main_controller' => '\\Workflow\\Controller_Workflow',
	'actionset_classes' => array(
		'\\Workflowadmin\\Controller_Workflowadmin' => array(
			'base'   => '\\Workflowadmin\\Actionset_Base_Workflowadmin',
			'index'  => '\\Workflowadmin\\Actionset_Index_Workflowadmin',
		),
	),
);
