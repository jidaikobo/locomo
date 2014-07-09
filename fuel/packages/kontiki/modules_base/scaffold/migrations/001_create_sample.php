<?php
namespace Fuel\Migrations;

class Create_sample
{
	public function up()
	{
		\DBUtil::create_table('samples', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'        => array('constraint' => 255, 'type' => 'varchar'),
			'status'      => array('constraint' => 20, 'type' => 'varchar'),
			'deleted_at'  => array('type' => 'datetime', 'null' => true),
			'created_at'  => array('type' => 'datetime', 'null' => true),
			'expired_at'  => array('type' => 'datetime', 'null' => true),
			'updated_at'  => array('type' => 'datetime', 'null' => true),
			'creator_id'  => array('constraint' => 5, 'type' => 'int'),
			'modifier_id' => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('samples');
	}
}