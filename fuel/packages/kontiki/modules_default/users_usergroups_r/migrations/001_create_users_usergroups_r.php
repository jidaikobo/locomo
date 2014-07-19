<?php
namespace Fuel\Migrations;
class Create_users_usergroups_r
{
	public function up()
	{
		\DBUtil::create_table('users_usergroups_r', array(
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int'),

		), array('user_id','usergroup_id'));
	}

	public function down()
	{
		\DBUtil::drop_table('users_usergroups_r');
	}
}