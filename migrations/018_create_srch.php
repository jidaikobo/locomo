<?php
namespace Fuel\Migrations;
class Create_srch
{
	public function up()
	{
		\DBUtil::create_table('lcm_srches', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'controller' => array('constraint' => 255, 'type' => 'varchar'),
			'pid' => array('constraint' => 5, 'type' => 'int'),
			'url' => array('type' => 'text'),
			'seacrh' => array('type' => 'text'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_srches');
	}
}
