<?php
namespace Fuel\Migrations;
class Create_usergroups
{
	public function up()
	{
		//usergroups
		\DBUtil::create_table('usergroups', array(
			'id'             => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'usergroup_name'  => array('constraint' => 50, 'type' => 'varchar'),
			'workflow_status' => array('constraint' => 20, 'type' => 'varchar'),
			'deleted_at'      => array('type' => 'datetime', 'null' => true),
			'created_at'      => array('type' => 'datetime', 'null' => true),
			'expired_at'      => array('type' => 'datetime', 'null' => true),
			'updated_at'      => array('type' => 'datetime', 'null' => true),
		), array('id'));

		//users_usergroups_r
		\DBUtil::create_table('users_usergroups_r', array(
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int'),
		), array('user_id','usergroup_id'));

	}

	public function down()
	{
		\DBUtil::drop_table('usergroups');
		\DBUtil::drop_table('users_usergroups_r');
	}
}