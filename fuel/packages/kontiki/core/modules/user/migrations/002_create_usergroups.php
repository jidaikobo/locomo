<?php
namespace Fuel\Migrations;
class Create_usergroups
{
	public function up()
	{
		//usergroups
		\DBUtil::create_table('usergroups', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'         => array('constraint' => 50, 'type' => 'varchar'),
			'description'  => array('constraint' => 255, 'type' => 'varchar'),
			'order'        => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'is_available' => array('constraint' => 1, 'type' => 'tinyint'),
			'deleted_at'   => array('type' => 'datetime', 'null' => true),
		), array('id'));

		//usergroups_r
		\DBUtil::create_table('usergroups_r', array(
			'user_id'   => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'group_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
		), array('user_id','group_id'));
	}

	public function down()
	{
		\DBUtil::drop_table('usergroups');
		\DBUtil::drop_table('usergroups_r');
	}
}
