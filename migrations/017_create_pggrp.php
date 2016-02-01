<?php
namespace Fuel\Migrations;
class Create_pggrp
{
	public function up()
	{
		\DBUtil::create_table('lcm_pggrps', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'summary' => array('type' => 'text', 'default' => ''),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'is_available' => array('type' => 'bool', 'null' => true),
			'creator_id' => array('constraint' => 5, 'type' => 'int'),
			'updater_id' => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_pggrps');
	}
}
