<?php
namespace Fuel\Migrations;
class Create_workflow
{
	public function up()
	{
		//workflows
		\DBUtil::create_table('workflows', array(
			'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'       => array('constraint' => 50, 'type' => 'varchar'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('workflows', array('deleted_at'), 'workflows_idx_deleted_at');

		//workflow_steps
		\DBUtil::create_table('workflow_steps', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'        => array('constraint' => 200, 'type' => 'varchar'),
			'workflow_id' => array('constraint' => 11, 'type' => 'int'),
			'condition'   => array('constraint' => 50, 'type' => 'varchar'),
			'seq'         => array('constraint' => 11, 'type' => 'int'),
			'action'      => array('constraint' => 200, 'type' => 'varchar'),
		), array('id'));
		\DBUtil::create_index('workflow_steps', array('workflow_id'), 'workflow_steps_idx_workflow_id');
		\DBUtil::create_index('workflow_steps', array('seq'), 'workflow_steps_idx_seq');

		//workflow_allowers
		\DBUtil::create_table('workflow_allowers', array(
			'workflow_id'  => array('constraint' => 11, 'type' => 'int', 'comment' => 'redundant field'),
			'step_id'      => array('constraint' => 11, 'type' => 'int'),
			'user_id'      => array('constraint' => 11, 'type' => 'int'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int'),
			'is_writer'    => array('constraint' => 1, 'type' => 'int'),
		), array());
		\DBUtil::create_index('workflow_allowers', array('step_id','user_id','is_writer'), 'workflow_allowers_idx_user_id');
		\DBUtil::create_index('workflow_allowers', array('step_id','usergroup_id','is_writer'), 'workflow_allowers_idx_usergroup_id');

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
		\DBUtil::create_index('workflow_logs', array('workflow_id'), 'workflow_logs_idx_workflow_id');
		\DBUtil::create_index('workflow_logs', array('controller_id'), 'workflow_logs_idx_controller_id');
		\DBUtil::create_index('workflow_logs', array('current_step'), 'workflow_logs_idx_current_step');
		\DBUtil::create_index('workflow_logs', array('status'), 'workflow_logs_idx_status');
		\DBUtil::create_index('workflow_logs', array('created_at'), 'workflow_logs_idx_created_at');
		\DBUtil::create_index('workflow_logs', array('did_user_id'), 'workflow_logs_idx_did_user_id');

		//workflow_current_users
		\DBUtil::create_table('workflow_current_users', array(
			'log_id'        => array('constraint' => 11, 'type' => 'int'),
			'controller'    => array('constraint' => 50, 'type' => 'varchar'),
			'controller_id' => array('constraint' => 11, 'type' => 'int'),
			'user_id'       => array('constraint' => 11, 'type' => 'int'),
		), array());
		\DBUtil::create_index('workflow_current_users', array('log_id'), 'workflow_cusers_idx_log_id');
		\DBUtil::create_index('workflow_current_users', array('controller_id'), 'workflow_cusers_idx_controller_id');
		\DBUtil::create_index('workflow_current_users', array('user_id'), 'workflow_cusers_idx_user_id');
	}

	public function down()
	{
		// workflows
		\DBUtil::drop_index('workflows', 'workflows_idx_deleted_at');

		// workflow_steps
		\DBUtil::drop_index('workflow_steps', 'workflow_steps_idx_workflow_id');
		\DBUtil::drop_index('workflow_steps', 'workflow_steps_idx_seq');
		\DBUtil::drop_index('workflow_steps', 'workflow_steps_idx_is_writer');

		// workflow_allowers
		\DBUtil::drop_index('workflow_allowers', 'workflow_allowers_idx_user_id');
		\DBUtil::drop_index('workflow_allowers', 'workflow_allowers_idx_usergroup_id');

		// workflow_logs
		\DBUtil::drop_index('workflow_logs', 'workflow_logs_idx_workflow_id');
		\DBUtil::drop_index('workflow_logs', 'workflow_logs_idx_controller_id');
		\DBUtil::drop_index('workflow_logs', 'workflow_logs_idx_current_step');
		\DBUtil::drop_index('workflow_logs', 'workflow_logs_idx_status');
		\DBUtil::drop_index('workflow_logs', 'workflow_logs_idx_created_at');
		\DBUtil::drop_index('workflow_logs', 'workflow_logs_idx_did_user_id');

		// workflow_current_users
		\DBUtil::drop_index('workflow_current_users', 'workflow_cusers_idx_log_id');
		\DBUtil::drop_index('workflow_current_users', 'workflow_cusers_idx_controller_id');
		\DBUtil::drop_index('workflow_current_users', 'workflow_cusers_idx_user_id');

		// drop_table
		\DBUtil::drop_table('workflows');
		\DBUtil::drop_table('workflow_steps');
		\DBUtil::drop_table('workflow_step_actions');
		\DBUtil::drop_table('workflow_allowers');
		\DBUtil::drop_table('workflow_logs');
	}
}