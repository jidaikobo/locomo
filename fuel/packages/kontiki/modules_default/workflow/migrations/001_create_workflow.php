<?php
namespace Fuel\Migrations;
class Create_workflow
{
	public function up()
	{
		//workflows
		\DBUtil::create_table('workflows', array(
			'id'   => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
		), array('id'));

		//workflow_steps
		\DBUtil::create_table('workflow_steps', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'        => array('constraint' => 200, 'type' => 'varchar'),
			'workflow_id' => array('constraint' => 11, 'type' => 'int'),
			'condition'   => array('constraint' => 50, 'type' => 'varchar'),
			'order'       => array('constraint' => 11, 'type' => 'int'),
		), array('id'));

		//workflow_step_actions
		\DBUtil::create_table('workflow_step_actions', array(
			'id'      => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'step_id' => array('constraint' => 11, 'type' => 'int'),
			'action'  => array('constraint' => 50, 'type' => 'varchar'),
		), array('id'));

		//workflow_allowers
		\DBUtil::create_table('workflow_allowers', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'step_id'      => array('constraint' => 11, 'type' => 'int'),
			'user_id'      => array('constraint' => 11, 'type' => 'int'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int'),
		), array('id'));

		//workflow_logs
		\DBUtil::create_table('workflow_logs', array(
			'id'            => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'workflow_id'   => array('constraint' => 11, 'type' => 'int'),
			'controller'    => array('constraint' => 50, 'type' => 'varchar'),
			'controller_id' => array('constraint' => 11, 'type' => 'int'),
			'current_step'  => array('constraint' => 11, 'type' => 'int'),
			'status'        => array('constraint' => 50, 'type' => 'varchar'),
			'comment'       => array('type' => 'text'),
			'created_at'    => array('type' => 'datetime', 'null' => true),
			'creator_id'    => array('constraint' => 11, 'type' => 'int'),
		), array('id'));
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