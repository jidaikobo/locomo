<?php

namespace Fuel\Migrations;

class Create_meta
{
	public function up()
	{
		\DBUtil::create_table('meta', array(
			'controller' => array('constraint' => 255, 'type' => 'varchar'),
			'controller_id' => array('constraint' => 11, 'type' => 'int'),
			'meta_key' => array('constraint' => 255, 'type' => 'varchar'),
			'meta_value' => array('type' => 'longtext'),
		), array());
	}

	public function down()
	{
		\DBUtil::drop_table('meta');
	}
}