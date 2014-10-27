<?php
namespace Fuel\Migrations;
class Create_option
{
	public function up()
	{
		//options
		\DBUtil::create_table('options', array(
			'id'           => array('constraint' => 11,  'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'controller'   => array('constraint' => 50,  'type' => 'varchar'),
			'name'         => array('constraint' => 50,  'type' => 'varchar'),
			'sub_name'     => array('constraint' => 255, 'type' => 'varchar'),
			'label'        => array('constraint' => 255, 'type' => 'varchar'),
			'value'        => array('constraint' => 255, 'type' => 'varchar'),
			'description'  => array('constraint' => 255, 'type' => 'varchar'),
			'order'        => array('constraint' => 11,  'type' => 'int', 'unsigned' => true),
			'is_available' => array('constraint' => 1,   'type' => 'tinyint'),
		), array('id'));

		//options_r
		\DBUtil::create_table('options_r', array(
			'item_id'    => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'option_id'  => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'controller' => array('constraint' => 50, 'type' => 'varchar'),
		), array('item_id','option_id','controller'));
	}

	public function down()
	{
		\DBUtil::drop_table('options');
		\DBUtil::drop_table('options_r');
	}
}