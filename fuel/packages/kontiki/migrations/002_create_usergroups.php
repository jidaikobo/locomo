<?php

namespace Fuel\Migrations;

class Create_usergroups
{
	public function up()
	{
		\DBUtil::create_table('usergroups', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'usergroup_name' => array('constraint' => 50, 'type' => 'varchar'),
			'deleted_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('usergroups');
	}
}