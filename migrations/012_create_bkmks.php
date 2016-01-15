<?php
namespace Fuel\Migrations;
class Create_Bkmks
{
	public function up()
	{
		echo "create lcm_bkmks table.\n";
		\DBUtil::create_table('lcm_bkmks', array(
			'id'               => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'user_id'          => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'name'             => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'url'              => array('type' => 'text',     'default' => ''),
			'seq'              => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
		), array('id'));
	}

	public function down()
	{
		echo "drop lcm_bkmks table.\n";
		\DBUtil::drop_table('lcm_bkmks');
	}
}
