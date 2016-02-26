<?php
namespace Fuel\Migrations;
class Create_pgfl
{
	public function up()
	{
		\DBUtil::create_table('lcm_pgfls', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'pg_id' => array('constraint' => 5, 'type' => 'int'),
			'name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'path' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'url' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'alt' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'creator_id' => array('constraint' => 5, 'type' => 'int'),
			'updater_id' => array('constraint' => 5, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_pgfls');
	}
}
