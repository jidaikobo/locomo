<?php
namespace Fuel\Migrations;
class Create_postcategories
{
	public function up()
	{
		\DBUtil::create_table('postcategories', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'         => array('constraint' => 50, 'type' => 'varchar'),
			'order'        => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'is_available' => array('constraint' => 1, 'type' => 'tinyint'),
		), array('id'));
		
		\DBUtil::create_table('postcategories_r', array(
			'item_id'   => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'option_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
		), array('item_id','option_id'));
	}

	public function down()
	{
		\DBUtil::drop_table('postcategories');
		\DBUtil::drop_table('postcategories_r');
	}
}
