<?php
namespace Fuel\Migrations;
class Create_Revision
{
	public function up()
	{
		echo "create lcm_revisions table.\n";
		\DBUtil::create_table('lcm_revisions', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'model'       => array('constraint' => 50, 'type' => 'varchar'),
			'pk_id'       => array('constraint' => 11, 'type' => 'int'),
			'data'        => array('type' => 'longtext'),
			'comment'     => array('type' => 'text'),
			'operation'   => array('type' => 'text'),
			'created_at'  => array('type' => 'datetime', 'null' => true),
			'deleted_at'  => array('type' => 'datetime', 'null' => true),
			'user_id' => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		echo "drop revisions related tables.\n";
		\DBUtil::drop_table('lcm_revisions');
	}
}
