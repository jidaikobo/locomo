<?php
namespace Fuel\Migrations;
class Create_Flrs
{
	public function up()
	{
		// lcm_flrs
		echo "create lcm_flrs table.\n";
		\DBUtil::create_table('lcm_flrs', array(
			'id'           => array('constraint' => 11,  'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'         => array('constraint' => 1024, 'type' => 'varchar'),
			'explanation'  => array('type' => 'text'),
			'path'         => array('constraint' => 1024, 'type' => 'varchar', 'unique' => true),
			'depth'        => array('constraint' => 5,   'type' => 'int'),
			'is_visible'   => array('constraint' => 5,   'type' => 'int'),
			'is_sticky'    => array('constraint' => 5,   'type' => 'int'),
			'ext'          => array('constraint' => 10,  'type' => 'varchar'),
			'mimetype'     => array('constraint' => 20,  'type' => 'varchar'),
			'genre'        => array('constraint' => "'dir', 'file', 'txt', 'image', 'audio', 'movie', 'braille', 'doc', 'xls', 'ppt', 'pdf', 'compressed'",  'type' => "enum"),
			'deleted_at'   => array('type' => 'datetime', 'null' => true),
			'created_at'   => array('type' => 'datetime', 'null' => true),
			'expired_at'   => array('type' => 'datetime', 'null' => true),
			'updated_at'   => array('type' => 'datetime', 'null' => true),
			'creator_id'   => array('constraint' => 5, 'type' => 'int'),
			'updater_id'   => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
		\DBUtil::create_index('lcm_flrs', array('path'), 'flrs_path');

		// lcm_flr_permissions
		echo "create lcm_flr_permissions table.\n";
		\DBUtil::create_table('lcm_flr_permissions', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'flr_id'       => array('constraint' => 11, 'type' => 'int'),
			// access level
			// 1:reab+download 2:upload (rename file, purge file) 3:create dir 4:rename dir + move dir 5:purge dir
			'access_level' => array('constraint' => 5,  'type' => 'int', 'null' => true),
			'user_id'      => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'usergroup_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'deleted_at'   => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('lcm_flr_permissions', array('flr_id','user_id'), 'flrs_perm_uid');
		\DBUtil::create_index('lcm_flr_permissions', array('flr_id','usergroup_id'), 'flrs_perm_gid');
	}

	public function down()
	{
		echo "drop flr related tables.\n";
		\DBUtil::drop_table('lcm_flrs');
		\DBUtil::drop_table('lcm_flr_permissions');
	}
}
