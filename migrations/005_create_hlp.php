<?php
namespace Fuel\Migrations;
class Create_Hlp
{
	public function up()
	{
		echo "create lcm_hlps table.\n";
		\DBUtil::create_table('lcm_hlps', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'title'        => array('constraint' => 255, 'type' => 'varchar'),
			'ctrl'         => array('constraint' => 255, 'type' => 'varchar'),
			'body'         => array('type' => 'text'),
			'updated_at'   => array('type' => 'datetime', 'null' => true),
			'deleted_at'   => array('type' => 'datetime', 'null' => true),
			'created_at'   => array('type' => 'datetime', 'null' => true),
			'creator_id'   => array('constraint' => 5, 'type' => 'int'),
			'updater_id'  => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
		\DBUtil::create_index('lcm_hlps', array('ctrl'), 'lcm_hlps_ctrl');
	}

	public function down()
	{
		echo "drop hlp related tables.\n";
		\DBUtil::drop_table('lcm_hlps');
	}
}
