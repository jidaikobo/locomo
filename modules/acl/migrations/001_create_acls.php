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
		\DBUtil::create_index('acls', array('usergroup_id'), 'acls_idx_usergroup_id');
		\DBUtil::create_index('acls', array('user_id'), 'acls_idx_user_id');
	}

	public function down()
	{
		//\DBUtil::drop_index('acls','acls_idx_usergroup_id');
		//\DBUtil::drop_index('acls','acls_idx_user_id');
		\DBUtil::drop_table('acls');
	}
}
