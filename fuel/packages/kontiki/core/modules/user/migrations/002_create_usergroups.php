<?php
namespace Fuel\Migrations;
class Create_usergroups
{
	public function up()
	{
		//usergroups
		\DBUtil::create_table('usergroups', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'         => array('constraint' => 50, 'type' => 'varchar'),
			'description'  => array('constraint' => 255, 'type' => 'varchar'),
			'order'        => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'is_available' => array('constraint' => 1, 'type' => 'tinyint'),
		), array('id'));

		//usergroups_r
		\DBUtil::create_table('usergroups_r', array(
			'item_id'   => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'option_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
		), array('item_id','option_id'));
	}

	public function down()
	{
		\DBUtil::drop_table('usergroups');
		\DBUtil::drop_table('usergroups_r');
	}
}