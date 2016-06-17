<?php
namespace Fuel\Migrations;
class Create_pg
{
	public function up()
	{
		\DBUtil::create_table('lcm_pgs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'title' => array('type' => 'text', 'default' => ''),
			'path' => array('constraint' => 255, 'type' => 'varchar'),
			'url' => array('type' => 'text', 'default' => ''),
			'summary' => array('type' => 'text', 'default' => ''),
			'content' => array('type' => 'text', 'default' => ''),
			'lat' => array('constraint' => '8,6', 'type' => 'decimal', 'null' => true),
			'lng' => array('constraint' => '9,6', 'type' => 'decimal', 'null' => true),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'is_sticky' => array('type' => 'bool', 'null' => true),
			'is_visible' => array('type' => 'bool', 'null' => true),
			'is_available' => array('type' => 'bool', 'null' => true),
			'creator_id' => array('constraint' => 5, 'type' => 'int'),
			'updater_id' => array('constraint' => 5, 'type' => 'int'),
			'workflow_status' => array('constraint' => '"init","before_progress","in_progress","finish"', 'type' => 'enum', 'null' => true),
		), array('id'));
		\DBUtil::create_index('lcm_pgs', 'path', 'path');

		// lcm_pg_pggrps
		\DBUtil::create_table('lcm_pg_pggrps', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'pg_id' => array('constraint' => 255, 'type' => 'int'),
			'pggrp_id' => array('constraint' => 255, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_pgs');
		\DBUtil::drop_table('lcm_pg_pggrps');
	}
}
