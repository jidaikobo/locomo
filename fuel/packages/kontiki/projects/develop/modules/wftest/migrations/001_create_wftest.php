<?php
namespace Fuel\Migrations;
class Create_wftest
{
	public function up()
	{
		\DBUtil::create_table('wftests', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'title' => array('constraint' => 50, 'type' => 'varchar'),
			'body' => array('type' => 'text'),
			'status' => array('constraint' => 20, 'type' => 'varchar', 'null' => true),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'workflow_status' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('wftests');
	}
}
