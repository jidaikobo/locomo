<?php
namespace Fuel\Migrations;
class Create_admin_dashboards
{
	public function up()
	{
		\DBUtil::create_table('admin_dashboards', array(
			'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id'    => array('constraint' => 11, 'type' => 'int'),
			'action'     => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'size'       => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'seq'        => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('admin_dashboards', array('user_id'), 'admin_dsbrds_user_id');
	}

	public function down()
	{
		\DBUtil::drop_table('admin_dashboards');
	}
}
