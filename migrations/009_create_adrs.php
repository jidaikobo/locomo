<?php
namespace Fuel\Migrations;
class Create_adrs
{
	public function up()
	{
		\DBUtil::create_table('lcm_adrs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'company_kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'company_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'tel' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'fax' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'mail' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'zip3' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'zip4' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'address' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'memo' => array('type' => 'text', 'default' => ''),
			'mobile' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'group_id' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'creator_id' => array('constraint' => 5, 'type' => 'int'),
			'updater_id' => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
		\DBUtil::create_index('lcm_adrs', array('group_id'), 'adrs_group_id');
		\DBUtil::create_index('lcm_adrs', array('kana'), 'adrs_kana');
		\DBUtil::create_index('lcm_adrs', array('deleted_at'), 'adrs_deleted_at');

		\DBUtil::create_table('lcm_adrs_groups', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'description'  => array('constraint' => 255, 'type' => 'varchar'),
			'seq'          => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'is_available' => array('constraint' => 1, 'type' => 'tinyint'),

			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'creator_id' => array('constraint' => 5, 'type' => 'int', 'default' => -1),
			'updater_id' => array('constraint' => 5, 'type' => 'int', 'default' => -1),

		), array('id'));
		\DBUtil::create_index('lcm_adrs_groups', array('seq'), 'adrsgrp_seq');
		\DBUtil::create_index('lcm_adrs_groups', array('is_available'), 'adrsgrp_is_available');
		\DBUtil::create_index('lcm_adrs_groups', array('deleted_at'), 'adrsgrp_deleted_at');
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_adrs');
		\DBUtil::drop_table('lcm_adrs_groups');
	}
}
