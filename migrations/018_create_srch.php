<?php
namespace Fuel\Migrations;
class Create_srch
{
	public function up()
	{
		\DBUtil::create_table('lcm_srches', array(
			'id'         => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'title'      => array('constraint' => 255, 'type' => 'varchar'),
			'path'       => array('constraint' => 255, 'type' => 'varchar'),
			'pid'        => array('constraint' => 5, 'type' => 'int'),
			'url'        => array('type' => 'text'),
			'search'     => array('type' => 'text'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('lcm_srches', array('path'), 'srch_path');
		\DBUtil::create_index('lcm_srches', array('pid'), 'srch_pid');
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_srches');
	}
}
