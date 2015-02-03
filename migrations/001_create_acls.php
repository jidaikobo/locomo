<?php
namespace Fuel\Migrations;
class Create_Acls
{
	public function up()
	{
		echo "create lcm_acls table.\n";
		\DBUtil::create_table('lcm_acls', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'controller'   => array('constraint' => 100, 'type' => 'varchar'),
			'action'       => array('constraint' => 50, 'type' => 'varchar'),
			'slug'         => array('constraint' => 255, 'type' => 'varchar'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'user_id'      => array('constraint' => 11, 'type' => 'int', 'null' => true),
		), array('id','controller','action'));
		\DBUtil::create_index('lcm_acls', array('usergroup_id'), 'acls_ug_id');
		\DBUtil::create_index('lcm_acls', array('user_id'), 'acls_u_id');
	}

	public function down()
	{
		echo "drop lcm_acls table.\n";
		\DBUtil::drop_table('lcm_acls');
	}
}
