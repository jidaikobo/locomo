<?php
namespace Fuel\Migrations;
class Create_Dashboards
{
	public function up()
	{
		echo "create lcm_dashboards table.\n";
		\DBUtil::create_table('lcm_dashboards', array(
			'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id'    => array('constraint' => 11, 'type' => 'int'),
			'action'     => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'size'       => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'seq'        => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('lcm_dashboards', array('user_id'), 'dsbrds_user_id');
	}

	public function down()
	{
		echo "drop dashboard related tables.\n";
		\DBUtil::drop_table('lcm_dashboards');
	}
}
