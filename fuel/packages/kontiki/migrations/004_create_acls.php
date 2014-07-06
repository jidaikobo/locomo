<?php
namespace Fuel\Migrations;
class Create_acls
{
	public function up()
	{
		\DBUtil::create_table('acls', array(
			'controller'   => array('constraint' => 50, 'type' => 'varchar'),
			'action'       => array('constraint' => 50, 'type' => 'varchar'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'user_id'      => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'owner_auth'   => array('constraint' => 1, 'type' => 'int', 'null' => true),
		), array());
	}

	public function down()
	{
		\DBUtil::drop_table('acls');
	}
}