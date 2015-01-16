<?php
namespace Fuel\Migrations;
class Create_flrs
{
	public function up()
	{
		// lcm_flrs
		\DBUtil::create_table('lcm_flrs', array(
			'id'         => array('constraint' => 11,  'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'       => array('constraint' => 255, 'type' => 'varchar'),
			'path'       => array('constraint' => 255, 'type' => 'varchar'),
			'seq'        => array('constraint' => 11,  'type' => 'int', 'null' => true),
			'is_visible' => array('constraint' => 5,   'type' => 'int'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'creator_id' => array('constraint' => 5, 'type' => 'int'),
			'updater_id' => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
		\DBUtil::create_index('lcm_flrs', array('path'), 'flrs_path');

		// lcm_flr_permissions
		\DBUtil::create_table('lcm_flr_permissions', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'flr_id'       => array('constraint' => 11, 'type' => 'int'),
			'is_writable'  => array('constraint' => 5,  'type' => 'int', 'null' => true),
			'user_id'      => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('lcm_flr_permissions', array('flr_id','user_id'), 'flrs_perm_uid');
		\DBUtil::create_index('lcm_flr_permissions', array('flr_id','usergroup_id'), 'flrs_perm_gid');
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_flrs');
		\DBUtil::drop_table('lcm_flr_permissions');
	}
}
