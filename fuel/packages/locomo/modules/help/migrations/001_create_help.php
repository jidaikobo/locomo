<?php
namespace Fuel\Migrations;
class Create_help
{
	public function up()
	{
		\DBUtil::create_table('helps', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'title'       => array('constraint' => 255, 'type' => 'varchar'),
			'controller'  => array('constraint' => 255, 'type' => 'varchar'),
			'body'        => array('type' => 'text'),
			'updated_at'  => array('type' => 'datetime', 'null' => true),
			'deleted_at'  => array('type' => 'datetime', 'null' => true),
			'creator_id'  => array('constraint' => 5, 'type' => 'int'),
			'modifier_id' => array('constraint' => 5, 'type' => 'int'),
			'seq'         => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('helps');
	}
}
