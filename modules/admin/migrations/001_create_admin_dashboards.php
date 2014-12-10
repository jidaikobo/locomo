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
		\DBUtil::create_index('admin_dashboards', array('user_id'), 'admin_dashboards_idx_user_id');

		//default acl
		$arr = array(
			array('Admin\\Controller_Admin', 'edit', '\\Admin\\Controller_Admin/edit'),
			array('Admin\\Controller_Admin', 'clock', '\\Admin\\Controller_Admin/clock'),
			array('Admin\\Controller_Admin', 'clock', '\\Schedules\\Controller_Schedules/calendar'),
		);
		foreach($arr as $v):
			$slug = serialize(\Locomo\Auth_Acl_Locomoacl::_parse_conditions($v[2]));
			$query = \DB::insert('acls');
			$query->columns(array(
				'controller',
				'action',
				'slug',
				'usergroup_id',
			));
			$query->values(array($v[0], $v[1], $slug, -10));//'-10' means all logged in users
			$query->execute();
		endforeach;
	}

	public function down()
	{
		//\DBUtil::drop_index('acls','admin_dashboards_idx_user_id');
		\DBUtil::drop_table('admin_dashboards');
	}
}
