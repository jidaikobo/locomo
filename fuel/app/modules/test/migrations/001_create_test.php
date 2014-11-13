<?php
namespace Fuel\Migrations;
class Create_test
{
	public function up()
	{
		\DBUtil::create_table('tests', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'title' => array('constraint' => 255, 'type' => 'varchar', 'default' => 'title'),
			'body' => array('type' => 'text', 'default' => ''),
			'is_bool' => array('type' => 'bool', 'null' => true, 'default' => '0'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'is_visible' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'creator_id' => array('constraint' => 5, 'type' => 'int'),
			'modifier_id' => array('constraint' => 5, 'type' => 'int'),
			'workflow_status' => array('constraint' => '"init","approve","reject","remand","finish"', 'type' => 'enum', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('tests');
	}
}
