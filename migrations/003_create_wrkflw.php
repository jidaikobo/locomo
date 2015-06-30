<?php
namespace Fuel\Migrations;
class Create_Wrkflw
{
	public function up()
	{
		//workflows
		echo "create lcm_wrkflws table.\n";
		\DBUtil::create_table('lcm_wrkflws', array(
			'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'       => array('constraint' => 50, 'type' => 'varchar'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('lcm_wrkflws', array('deleted_at'), 'wf_deleted_at');

		//lcm_wrkflw_steps
		echo "create lcm_wrkflw_steps table.\n";
		\DBUtil::create_table('lcm_wrkflw_steps', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'        => array('constraint' => 200, 'type' => 'varchar'),
			'workflow_id' => array('constraint' => 11, 'type' => 'int'),
			'condition'   => array('constraint' => 50, 'type' => 'varchar'),
			'seq'         => array('constraint' => 11, 'type' => 'int'),
			'action'      => array('constraint' => 200, 'type' => 'varchar'),
		), array('id'));
		\DBUtil::create_index('lcm_wrkflw_steps', array('workflow_id'), 'wf_steps_workflow_id');
		\DBUtil::create_index('lcm_wrkflw_steps', array('seq'), 'wf_steps_seq');

		//lcm_wrkflw_allowers
		echo "create lcm_wrkflw_allowers table.\n";
		\DBUtil::create_table('lcm_wrkflw_allowers', array(
			'workflow_id'  => array('constraint' => 11, 'type' => 'int', 'comment' => 'redundant field'),
			'step_id'      => array('constraint' => 11, 'type' => 'int'),
			'user_id'      => array('constraint' => 11, 'type' => 'int'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int'),
			'is_writer'    => array('constraint' => 1, 'type' => 'int'),
		), array());
		\DBUtil::create_index('lcm_wrkflw_allowers', array('step_id','user_id','is_writer'), 'wf_allwrs_u_id');
		\DBUtil::create_index('lcm_wrkflw_allowers', array('step_id','usergroup_id','is_writer'), 'wf_allwrs_ug_id');

		//lcm_wrkflw_logs
		echo "create lcm_wrkflw_logs table.\n";
		\DBUtil::create_table('lcm_wrkflw_logs', array(
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
		\DBUtil::create_index('lcm_wrkflw_logs', array('workflow_id'), 'wf_logs_workflow_id');
		\DBUtil::create_index('lcm_wrkflw_logs', array('controller_id'), 'wf_logs_controller_id');
		\DBUtil::create_index('lcm_wrkflw_logs', array('current_step'), 'wf_logs_current_step');
		\DBUtil::create_index('lcm_wrkflw_logs', array('status'), 'wf_logs_status');
		\DBUtil::create_index('lcm_wrkflw_logs', array('created_at'), 'wf_logs_created_at');
		\DBUtil::create_index('lcm_wrkflw_logs', array('did_user_id'), 'wf_logs_did_user_id');

		//lcm_wrkflw_current_users
		echo "create lcm_wrkflw_current_users table.\n";
		\DBUtil::create_table('lcm_wrkflw_current_users', array(
			'log_id'        => array('constraint' => 11, 'type' => 'int'),
			'controller'    => array('constraint' => 50, 'type' => 'varchar'),
			'controller_id' => array('constraint' => 11, 'type' => 'int'),
			'user_id'       => array('constraint' => 11, 'type' => 'int'),
		), array());
		\DBUtil::create_index('lcm_wrkflw_current_users', array('log_id'), 'wf_cusers_log_id');
		\DBUtil::create_index('lcm_wrkflw_current_users', array('controller_id'), 'wf_cusers_controller_id');
		\DBUtil::create_index('lcm_wrkflw_current_users', array('user_id'), 'wf_cusers_user_id');
	}

	public function down()
	{
		echo "drop workflow related tables.\n";
		\DBUtil::drop_table('lcm_wrkflws');
		\DBUtil::drop_table('lcm_wrkflw_steps');
		\DBUtil::drop_table('lcm_wrkflw_step_actions');
		\DBUtil::drop_table('lcm_wrkflw_allowers');
		\DBUtil::drop_table('lcm_wrkflw_logs');
		\DBUtil::drop_table('lcm_wrkflw_current_users');
	}
}