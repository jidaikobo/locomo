<?php
namespace Fuel\Migrations;
class Create_msgbrd_Usr
{
	public function up()
	{
		// lcm_msgbrds_attaches
		\DBUtil::add_fields('lcm_msgbrds', array(
			'user_id'    => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'parent_id'  => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
		));

		\DBUtil::modify_fields('lcm_msgbrds', array(
			'usergroup_id' => array('constraint' => 11, 'type' => 'tinyint', 'null' => true, 'default' => null),
		));

		\DBUtil::create_table('lcm_msgbrds_opened', array(
			'id'        => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'msgbrd_id' => array('constraint' => 11, 'type' => 'tinyint', 'null' => true, 'default' => null),
			'user_id'   => array('constraint' => 11, 'type' => 'tinyint', 'null' => true, 'default' => null),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_fields('lcm_msgbrds', array(
			'user_id',
			'parent_id',
		));

		\DBUtil::drop_table('lcm_msgbrds_opened');
	}

	
}
