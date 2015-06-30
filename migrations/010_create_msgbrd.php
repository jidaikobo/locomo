<?php
namespace Fuel\Migrations;
class Create_msgbrd
{
	public function up()
	{
		// lcm_msgbrds
		\DBUtil::create_table('lcm_msgbrds', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'contents' => array('type' => 'text', 'default' => ''),
			'is_sticky' => array('type' => 'bool', 'null' => true, 'default' => '0'),
			'is_draft' => array('type' => 'bool', 'null' => true, 'default' => '0'),
			'category_id' => array('constraint' => 11, 'type' => 'tinyint', 'null' => true, 'default' => '0'),
			'usergroup_id' => array('constraint' => 11, 'type' => 'tinyint', 'null' => true, 'default' => '0'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'creator_id' => array('constraint' => 5, 'type' => 'int'),
			'updater_id' => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
		\DBUtil::create_index('lcm_msgbrds', array('usergroup_id'));

		// lcm_msgbrds_attaches
		\DBUtil::create_table('lcm_msgbrds_attaches', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'msgbrd_id' => array('constraint' => 11, 'type' => 'tinyint', 'null' => true, 'default' => '0'),
			'path' => array('constraint' => 1024, 'type' => 'varchar'),
		), array('id'));
		\DBUtil::create_index('lcm_msgbrds_attaches', array('msgbrd_id'));

		// lcm_msgbrdcategories
		\DBUtil::create_table('lcm_msgbrds_categories', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
			'description' => array('constraint' => 255, 'type' => 'varchar'),
			'seq' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'is_available' => array('constraint' => 1, 'type' => 'tinyint'),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('lcm_msgbrds_categories', array('seq'));
		\DBUtil::create_index('lcm_msgbrds_categories', array('is_available'));
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_msgbrds');
		\DBUtil::drop_table('lcm_msgbrds_attaches');
		\DBUtil::drop_table('lcm_msgbrds_categories');
	}
}
