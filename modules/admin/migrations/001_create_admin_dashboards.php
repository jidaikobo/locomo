<?php
namespace Fuel\Migrations;
class Create_admin_dashboards
{
	public function up()
	{
		\DBUtil::create_table('acls', array(
			'id'      => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'actions' => array('constraint' => 50, 'type' => 'varchar'),
		), array('id','user_id'));
		\DBUtil::create_index('admin_dashboards', array('user_id'), 'admin_dashboards_idx_user_id');
	}

	public function down()
	{
		\DBUtil::drop_index('acls','admin_dashboards_idx_user_id');
		\DBUtil::drop_table('admin_dashboards');
	}
}
