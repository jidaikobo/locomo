<?php
namespace Fuel\Migrations;
class Create_workflowadmin
{
	public function up()
	{
		//workflows
		\DBUtil::create_table('workflows', array(
			'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'       => array('constraint' => 50, 'type' => 'varchar'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));

		//workflow_steps
		\DBUtil::create_table('workflow_steps', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'        => array('constraint' => 200, 'type' => 'varchar'),
			'workflow_id' => array('constraint' => 11, 'type' => 'int'),
			'condition'   => array('constraint' => 50, 'type' => 'varchar'),
			'order'       => array('constraint' => 11, 'type' => 'int'),
			'action'      => array('constraint' => 200, 'type' => 'varchar'),
		), array('id'));

		//workflow_allowers
		\DBUtil::create_table('workflow_allowers', array(
			'step_id'      => array('constraint' => 11, 'type' => 'int'),
			'user_id'      => array('constraint' => 11, 'type' => 'int'),
		), array());

		//workflow_logs
		\DBUtil::create_table('workflow_logs', array(
			'id'            => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'workflow_id'   => array('constraint' => 11, 'type' => 'int'),
			'controller'    => array('constraint' => 50, 'type' => 'varchar'),
			'controller_id' => array('constraint' => 11, 'type' => 'int'),
			'current_step'  => array('constraint' => 11, 'type' => 'int'),
			'status'        => array('constraint' => 50, 'type' => 'varchar'),
			'created_at'    => array('type' => 'datetime', 'null' => true),
			'did_user_id'   => array('constraint' => 11, 'type' => 'int'),
			'comment'       => array('type' => 'text'),
		), array('id'));

		//workflow_current_users
		\DBUtil::create_table('workflow_current_users', array(
			'log_id'        => array('constraint' => 11, 'type' => 'int'),
			'controller'    => array('constraint' => 50, 'type' => 'varchar'),
			'controller_id' => array('constraint' => 11, 'type' => 'int'),
			'user_id'       => array('constraint' => 11, 'type' => 'int'),
		), array());
	}

	public function down()
	{
		\DBUtil::drop_table('workflows');
		\DBUtil::drop_table('workflow_steps');
		\DBUtil::drop_table('workflow_step_actions');
		\DBUtil::drop_table('workflow_allowers');
		\DBUtil::drop_table('workflow_logs');
	}
}