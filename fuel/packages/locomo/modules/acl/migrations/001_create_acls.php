<?php
namespace Fuel\Migrations;
class Create_acls
{
	public function up()
	{
		\DBUtil::create_table('acls', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'controller'   => array('constraint' => 100, 'type' => 'varchar'),
			'action'       => array('constraint' => 50, 'type' => 'varchar'),
			'slug'         => array('constraint' => 255, 'type' => 'varchar'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'user_id'      => array('constraint' => 11, 'type' => 'int', 'null' => true),
		), array('id','controller','action'));
		\DBUtil::create_index('acls', array('controller', 'action', 'slug', 'usergroup_id', 'user_id'), 'aclsindex');
	}

	public function down()
	{
		\DBUtil::drop_index('acls','aclsindex');
		\DBUtil::drop_table('acls');
	}
}
